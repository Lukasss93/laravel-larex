<?php

namespace Lukasss93\Larex\Importers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Contracts\Importer;

class JsonLanguagesImporter implements Importer
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Import data from JSON by language to CSV';
    }

    /**
     * @inheritDoc
     */
    public function handle(LarexImportCommand $command): Collection
    {
        $languages = collect([]);
        $rawValues = collect([]);

        //get all files
        $files = File::glob(resource_path('lang/*.json'));

        foreach ($files as $file) {
            $items = json_decode(File::get($file), true);
            $lang = pathinfo($file, PATHINFO_FILENAME);

            if (!$languages->contains($lang)) {
                $languages->push($lang);
            }

            foreach ($items as $keys => $value) {
                $composite = explode('.', $keys);
                $group = $composite[0];
                $key = implode('.', array_slice($composite, 1));

                $rawValues->push([
                    'group' => $group,
                    'key' => $key,
                    'lang' => $lang,
                    'value' => $value,
                ]);

            }
        }

        //creating the csv file
        $header = collect(['group', 'key'])->merge($languages);
        $headerCount = $header->count();
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

                for ($i = 2; $i < $headerCount; $i++) {
                    $real = $rawValue['lang'] === $header->get($i) ? $rawValue['value'] : '';
                    $output[$header->get($i)] = $real;
                }

                $data->push($output);
            } else {
                for ($i = 2; $i < $headerCount; $i++) {
                    $code = $rawValue['lang'] === $header->get($i) ? $rawValue['value'] : null;

                    if ($code !== null) {
                        $new = $data->get($index);
                        $new[$header->get($i)] = $rawValue['value'];
                        $data->put($index, $new);
                    }
                }
            }
        }

        return $data
            ->prepend($header->toArray())
            ->values();
    }
}
