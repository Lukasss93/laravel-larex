<?php

namespace Lukasss93\Larex\Exporters;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Contracts\Exporter;
use Lukasss93\Larex\Support\CsvParser;
use Lukasss93\Larex\Support\CsvReader;

class LaravelExporter implements Exporter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Export data from CSV to Laravel localization files';
    }

    /**
     * @inheritDoc
     */
    public function handle(LarexExportCommand $command, CsvReader $reader): int
    {
        $parser = CsvParser::create($reader);
        $languages = $parser->parse();

        foreach ($parser->getWarnings() as $warning) {
            $command->warn($warning);
        }

        $include = $command->option('include') !== null ? explode(',', $command->option('include')) : [];
        $exclude = $command->option('exclude') !== null ? explode(',', $command->option('exclude')) : [];
        $normalizeFolderName = $command->option('normalize-folder-name') === 'true';

        $eol = config('larex.eol', PHP_EOL);

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

            $folder = $normalizeFolderName ? str_replace('-', '_', $language) : $language;

            if (!File::exists(lang_path("$folder/"))) {
                File::makeDirectory(lang_path("$folder/"));
            }

            foreach ($groups as $group => $keys) {
                $write = fopen(lang_path("$folder/$group.php"), 'wb');
                fwrite(
                    $write,
                    /** @lang text */
                    "<?php$eol{$eol}return [$eol$eol"
                );

                foreach ($keys as $key => $value) {
                    self::writeKeyValue($key, $value, $write, 1, $eol);
                }

                fwrite($write, "$eol];$eol");

                fclose($write);
                $command->info(sprintf('%s created successfully.', lang_rpath("$folder/$group.php")));
            }
        }

        if ($found === 0) {
            $command->info('No entries exported.');
        }

        return 0;
    }

    protected static function writeKeyValue($key, $value, &$file, int $level = 1, $eol = PHP_EOL): void
    {
        $enclosure = '"';

        if (is_array($value)) {
            fwrite($file, str_repeat('    ', $level) . "'$key' => [$eol");
            $level++;
            foreach ($value as $childKey => $childValue) {
                self::writeKeyValue($childKey, $childValue, $file, $level, $eol);
            }
            fwrite($file, str_repeat('    ', $level - 1) . "],$eol");

            return;
        }

        $value = (string)$value;
        $value = str_replace(["'", '\\' . $enclosure], ["\'", $enclosure], $value);

        if (is_int($key) || (is_numeric($key) && ctype_digit($key))) {
            $key = (int)$key;
            fwrite($file, str_repeat('    ', $level) . "$key => '$value',$eol");
        } else {
            fwrite($file, str_repeat('    ', $level) . "'$key' => '$value',$eol");
        }
    }
}
