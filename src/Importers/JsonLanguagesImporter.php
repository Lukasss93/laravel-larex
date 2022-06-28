<?php

namespace Lukasss93\Larex\Importers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JsonException;
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
     * @throws JsonException
     */
    public function handle(LarexImportCommand $command): Collection
    {
        $include = Str::of($command->option('include'))->explode(',')->reject(fn ($i) => empty($i));
        $exclude = Str::of($command->option('exclude'))->explode(',')->reject(fn ($i) => empty($i));

        /** @var Collection<int,string> $languages */
        $languages = collect([]);

        /** @var Collection<int,array> $rawValues */
        $rawValues = collect([]);

        //get all files
        $files = File::glob(lang_path('*.json'));

        foreach ($files as $file) {
            $items = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);
            $lang = pathinfo($file, PATHINFO_FILENAME);

            if ($include->isNotEmpty() && !$include->contains($lang)) {
                continue;
            }

            if ($exclude->isNotEmpty() && $exclude->contains($lang)) {
                continue;
            }

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
        /** @var Collection<int,array> $data */
        $data = collect([]);

        foreach ($rawValues as $rawValue) {
            $index = $data->search(fn ($item) => $item['group'] === $rawValue['group'] && $item['key'] === $rawValue['key']);

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
