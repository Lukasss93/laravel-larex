<?php

namespace Lukasss93\Larex\Importers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Contracts\Importer;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class LaravelImporter implements Importer
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Import data from Laravel localization files to CSV';
    }

    /**
     * @inheritDoc
     */
    public function handle(LarexImportCommand $command): Collection
    {
        $include = Str::of($command->option('include'))->explode(',')->reject(fn ($i) => empty($i));
        $exclude = Str::of($command->option('exclude'))->explode(',')->reject(fn ($i) => empty($i));
        $languages = collect([]);
        $rawValues = collect([]);

        //get all files
        $files = File::glob(resource_path('lang/**/*.php'));

        foreach ($files as $file) {
            $items = include $file;
            $group = pathinfo($file, PATHINFO_FILENAME);
            $lang = str_replace('_', '-', basename(dirname($file)));

            if ($include->isNotEmpty() && !$include->contains($lang)) {
                continue;
            }

            if ($exclude->isNotEmpty() && $exclude->contains($lang)) {
                continue;
            }

            if (!$languages->contains($lang)) {
                $languages->push($lang);
            }

            //loop through array recursive
            $iterator = new RecursiveIteratorIterator(
                new RecursiveArrayIterator($items),
                RecursiveIteratorIterator::SELF_FIRST
            );

            $path = [];
            foreach ($iterator as $key => $value) {
                $path[$iterator->getDepth()] = $key;
                if (!is_array($value)) {
                    $rawValues->push([
                        'group' => $group,
                        'key' => implode('.', array_slice($path, 0, $iterator->getDepth() + 1)),
                        'lang' => $lang,
                        'value' => $value,
                    ]);
                }
            }
        }

        //creating the csv file
        $data = collect([]);

        foreach ($rawValues as $rawValue) {
            $index = $data->search(function ($item) use ($rawValue) {
                return $item['group'] === $rawValue['group'] && $item['key'] === $rawValue['key'];
            });

            if ($index === false) {
                $output = [
                    'group' => $rawValue['group'],
                    'key' => $rawValue['key'],
                ];

                foreach ($languages as $lang) {
                    $real = $rawValue['lang'] === $lang ? $rawValue['value'] : '';
                    $output[$lang] = $real;
                }

                $data->push($output);
            } else {
                foreach ($languages as $lang) {
                    $code = $rawValue['lang'] === $lang ? $rawValue['value'] : null;

                    if ($code !== null) {
                        $new = $data->get($index);
                        $new[$lang] = $rawValue['value'];
                        $data->put($index, $new);
                    }
                }
            }
        }

        return $data->values();
    }
}
