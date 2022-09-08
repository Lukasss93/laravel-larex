<?php

namespace Lukasss93\Larex\Exporters;

use Illuminate\Support\Facades\File;
use JsonException;
use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Contracts\Exporter;
use Lukasss93\Larex\Support\CsvParser;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\Utils;

class JsonGroupsExporter implements Exporter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Export data from CSV to JSON by group';
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function handle(LarexExportCommand $command, CsvReader $reader): int
    {
        $parser = CsvParser::create($reader);
        $languages = $parser->setHandleSubKey(false)->parse();

        foreach ($parser->getWarnings() as $warning) {
            $command->warn($warning);
        }

        $include = $command->option('include') !== null ? (explode(',', $command->option('include'))) : [];
        $exclude = $command->option('exclude') !== null ? explode(',', $command->option('exclude')) : [];

        //finally save the files
        $found = 0;
        foreach ($languages as $language => $groups) {
            if (count($include) > 0 && !in_array($language, $include, true)) {
                continue;
            }
            if (count($exclude) > 0 && in_array($language, $exclude, true)) {
                continue;
            }
            $found++;

            if (!File::exists(lang_path("$language/"))) {
                File::makeDirectory(lang_path("$language/"));
            }

            foreach ($groups as $group => $keys) {
                Utils::putJson(lang_path("$language/$group.json"), $keys);
                $command->info(sprintf("%s created successfully.", lang_rpath("$language/$group.json")));
            }
        }

        if ($found === 0) {
            $command->info('No entries exported.');
        }

        return 0;
    }
}
