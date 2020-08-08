<?php

namespace Lukasss93\Larex;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Utils
{
    /**
     * Get a collection from csv file
     * @param string $filename
     * @return Collection
     */
    public static function csvToCollection(string $filename): Collection
    {
        $output = collect([]);
        $file = fopen($filename, 'rb');
        while (($columns = fgetcsv($file, 0, ';')) !== false) {
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
            self::fputcsv($file, $row, ';');
        }
        fclose($file);
    }
    
    public static function getStub(string $name): string
    {
        $content = file_get_contents(__DIR__ . '/Stubs/' . $name . '.stub');
        return self::normalizeEOLs($content);
    }
    
    public static function normalizeEOLs(string $content): string
    {
        return preg_replace('/\r\n|\r|\n/', PHP_EOL, $content);
    }
    
    public static function writeKeyValue($key, $value, &$file, int $level = 1): void
    {
        if (is_array($value)) {
            fwrite($file, str_repeat('    ', $level) . "'$key' => [" . PHP_EOL);
            $level++;
            foreach ($value as $childKey => $childValue) {
                self::writeKeyValue($childKey, $childValue, $file, $level);
            }
            fwrite($file, str_repeat('    ', $level - 1) . "]," . PHP_EOL);
            return;
        }
        
        $value = (string)$value;
        $value = str_replace("'", "\'", $value);
        fwrite($file, str_repeat('    ', $level) . "'$key' => '$value'," . PHP_EOL);
    }
    
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
    
    public static function fputcsv($handle, $array, $delimiter = ',', $enclosure = '"', $eol = PHP_EOL)
    {
        $output = '';
        
        $count = count($array);
        foreach ($array as $i => $item) {
            $item = self::normalizeEOLs($item);
            
            if (Str::contains($item, $enclosure)) {
                $item = str_replace($enclosure, "\\" . $enclosure, $item);
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
