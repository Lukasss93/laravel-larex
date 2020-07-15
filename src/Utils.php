<?php

namespace Lukasss93\Larex;

use Illuminate\Support\Collection;
use Keboola\Csv\CsvOptions;
use Keboola\Csv\CsvReader;
use Keboola\Csv\CsvWriter;
use Keboola\Csv\Exception;
use Keboola\Csv\InvalidArgumentException;

class Utils
{
    
    /**
     * Get a collection from csv file
     * @param string $filename
     * @return Collection
     * @throws Exception
     */
    public static function csvToCollection(string $filename): Collection
    {
        $output = collect([]);
        $reader = new CsvReader($filename, ';', CsvOptions::DEFAULT_ENCLOSURE, CsvOptions::DEFAULT_ESCAPED_BY);
        foreach($reader as $row) {
            $output->push($row);
        }
        return $output;
    }
    
    /**
     * Write a collection to csv file
     * @param Collection $array
     * @param string $filename
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public static function collectionToCsv(Collection $array, string $filename): void
    {
        $writer = new CsvWriter($filename, ';', null, PHP_EOL);
        foreach($array as $row) {
            $writer->writeRow($row);
        }
    }
    
    public static function getStub(string $name): string
    {
        $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . $name . '.stub');
        return self::normalizeEOLs($content);
    }
    
    public static function normalizeEOLs(string $content): string
    {
        return preg_replace('/\r\n|\r|\n/', PHP_EOL, $content);
    }
    
    public static function writeKeyValue($key, $value, &$file, int $level = 1): void
    {
        if(is_array($value)) {
            fwrite($file, str_repeat('    ', $level) . "'$key' => [" . PHP_EOL);
            $level++;
            foreach($value as $childKey => $childValue) {
                self::writeKeyValue($childKey, $childValue, $file, $level);
            }
            fwrite($file, str_repeat('    ', $level - 1) . "]," . PHP_EOL);
            return;
        }
        
        $value = (string)$value;
        $value = str_replace("'", "\'", $value);
        fwrite($file, str_repeat('    ', $level) . "'$key' => '$value'," . PHP_EOL);
    }
    
}
