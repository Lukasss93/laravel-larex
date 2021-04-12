<?php

namespace Lukasss93\Larex\Importers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use JsonException;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Contracts\Importer;

class JsonGroupsImporter implements Importer
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Import data from JSON by group to CSV';
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function handle(LarexImportCommand $command): Collection
    {
        $languages = collect([]);
        $rawValues = collect([]);

        //get all files
        $files = File::glob(resource_path('lang/**/*.json'));

        foreach ($files as $file) {
            $items = json_decode(File::get($file), true, 512, JSON_THROW_ON_ERROR);
            $group = pathinfo($file, PATHINFO_FILENAME);
            $lang = basename(dirname($file));

            if (!$languages->contains($lang)) {
                $languages->push($lang);
            }

            foreach ($items as $key => $value) {
                $rawValues->push([
                    'group' => $group,
                    'key' => $key,
                    'lang' => $lang,
                    'value' => $value,
                ]);
            }
        }

        //creating the csv file
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
