<?php

namespace Lukasss93\Larex\Exporters;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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

        $include = $command->option('include') !== null ? (explode(',', $command->option('include'))) : [];
        $exclude = $command->option('exclude') !== null ? explode(',', $command->option('exclude')) : [];
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

            $folder = Str::replace('-', '_', $language);

            if (!File::exists(resource_path("lang/$folder/"))) {
                File::makeDirectory(resource_path("lang/$folder/"));
            }

            foreach ($groups as $group => $keys) {
                $write = fopen(resource_path("lang/$folder/$group.php"), 'wb');
                fwrite($write, /** @lang text */ "<?php$eol{$eol}return [$eol$eol");

                foreach ($keys as $key => $value) {
                    self::writeKeyValue($key, $value, $write, 1, $eol);
                }

                fwrite($write, "$eol];$eol");

                fclose($write);
                $command->info("resources/lang/$folder/$group.php created successfully.");
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
            fwrite($file, str_repeat('    ', $level)."'$key' => [$eol");
            $level++;
            foreach ($value as $childKey => $childValue) {
                self::writeKeyValue($childKey, $childValue, $file, $level, $eol);
            }
            fwrite($file, str_repeat('    ', $level - 1)."],$eol");

            return;
        }

        $value = (string) $value;
        $value = str_replace(["'", '\\'.$enclosure], ["\'", $enclosure], $value);

        if (is_int($key) || (is_numeric($key) && ctype_digit($key))) {
            $key = (int) $key;
            fwrite($file, str_repeat('    ', $level)."$key => '$value',$eol");
        } else {
            fwrite($file, str_repeat('    ', $level)."'$key' => '$value',$eol");
        }
    }
}
