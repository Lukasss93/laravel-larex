<?php

namespace Lukasss93\Larex;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Utils
{
    public const CSV_DEFAULT_PATH = 'resources/lang/localization.csv';
    private const CSV_DELIMITER = ';';
    private const CSV_ENCLOSURE = '"';
    private const CSV_ESCAPE = '\\';

    /**
     * Get a collection from csv file
     * @param string $filename
     * @return Collection
     */
    public static function csvToCollection(string $filename): Collection
    {
        $output = collect([]);
        $file = fopen($filename, 'rb');
        while (($columns = fgetcsv($file, 0, self::CSV_DELIMITER)) !== false) {
            $output->push($columns);
        }
        fclose($file);
        return $output;
    }

    /**
     * Write a collection to csv file
     * @param Collection $array
     * @param string $filename
     */
    public static function collectionToCsv(Collection $array, string $filename): void
    {
        $file = fopen($filename, 'wb');
        foreach ($array as $row) {
            self::fputcsv(
                $file,
                $row,
                self::CSV_DELIMITER,
                self::CSV_ENCLOSURE,
                self::CSV_ESCAPE
            );
        }
        fclose($file);
    }

    /**
     * Get a stub file
     * @param string $name
     * @return string
     */
    public static function getStub(string $name): string
    {
        $content = file_get_contents(__DIR__ . '/Stubs/' . $name . '.stub');
        return self::normalizeEOLs($content);
    }

    /**
     * Normalize EOLs
     * @param string $content
     * @return string
     */
    public static function normalizeEOLs(string $content): string
    {
        return preg_replace('/\r\n|\r|\n/', PHP_EOL, $content);
    }

    /**
     * Write key/value for php files
     * @param $key
     * @param $value
     * @param $file
     * @param int $level
     */
    public static function writeKeyValue($key, $value, &$file, int $level = 1): void
    {
        if (is_array($value)) {
            fwrite($file, str_repeat('    ', $level) . "'{$key}' => [" . PHP_EOL);
            $level++;
            foreach ($value as $childKey => $childValue) {
                self::writeKeyValue($childKey, $childValue, $file, $level);
            }
            fwrite($file, str_repeat('    ', $level - 1) . '],' . PHP_EOL);
            return;
        }

        $value = (string)$value;
        $value = str_replace(
            ["'", '\\' . self::CSV_ENCLOSURE],
            ["\'", self::CSV_ENCLOSURE],
            $value
        );

        if (is_int($key) || (is_numeric($key) && ctype_digit($key))) {
            $key = (int)$key;
            fwrite($file, str_repeat('    ', $level) . "{$key} => '{$value}'," . PHP_EOL);
        } else {
            fwrite($file, str_repeat('    ', $level) . "'{$key}' => '{$value}'," . PHP_EOL);
        }
    }

    /**
     * Loop "forever"
     * @param callable $callback
     */
    public static function forever(callable $callback): void
    {
        $env = getenv('NOLOOP');

        if ($env !== '1') {
            while (true) {
                $callback();
            }
        } else {
            $callback();
        }
    }

    /**
     * Format line as CSV and write to file pointer
     * @param $handle
     * @param $array
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param string $eol
     * @return bool|int
     */
    public static function fputcsv($handle, $array, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = PHP_EOL)
    {
        $output = '';

        $count = count($array);
        foreach ($array as $i => $item) {
            $item = self::normalizeEOLs($item);

            if (Str::contains($item, $enclosure)) {
                $item = $enclosure . str_replace($enclosure, $escape . $enclosure, $item) . $enclosure;
            }

            if (Str::contains($item, [$delimiter, PHP_EOL])) {
                $item = $enclosure . $item . $enclosure;
            }

            $output .= $item;

            if ($i < $count - 1) {
                $output .= $delimiter;
            }
        }
        $output .= $eol;

        return fwrite($handle, $output);
    }
}
