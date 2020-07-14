<?php

namespace Lukasss93\Larex;

use Illuminate\Support\Collection;

class Utils
{
    
    public static function csvToCollection(string $filename): Collection
    {
        $output = collect([]);
        $file = fopen($filename, 'rb');
        while(($row = fgetcsv($file)) !== false) {
            $columns = str_getcsv($row[0], ';');
            $output->push($columns);
        }
        fclose($file);
        return $output;
    }
    
    public static function collectionToCsv(Collection $array, string $filename): void
    {
        $file = fopen($filename, 'wb');
        foreach($array as $row) {
            fputcsv($file, $row, ';');
        }
        fclose($file);
    }
    
    public static function getStub(string $name): string
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . $name . '.stub');
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
