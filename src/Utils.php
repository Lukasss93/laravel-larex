<?php

namespace Lukasss93\Larex;

use DOMDocument;
use Exception;
use Fuse\Fuse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
        $delimiter = config('larex.csv.delimiter');
        while (($columns = fgetcsv($file, 0, $delimiter)) !== false) {
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
        $delimiter = config('larex.csv.delimiter');
        $enclosure = config('larex.csv.enclosure');
        $escape = config('larex.csv.escape');
        foreach ($array as $row) {
            self::fputcsv($file, $row, $delimiter, $enclosure, $escape);
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
     * @param string|null $content
     * @param string $replace
     * @return string
     */
    public static function normalizeEOLs(?string $content, string $replace = "\n"): string
    {
        return preg_replace('/\r\n|\r|\n/', $replace, $content ?? '');
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
        $enclosure = config('larex.csv.enclosure');
        
        if (is_array($value)) {
            fwrite($file, str_repeat('    ', $level) . "'{$key}' => [\n");
            $level++;
            foreach ($value as $childKey => $childValue) {
                self::writeKeyValue($childKey, $childValue, $file, $level);
            }
            fwrite($file, str_repeat('    ', $level - 1) . "],\n");
            return;
        }
        
        $value = (string)$value;
        $value = str_replace(["'", '\\' . $enclosure], ["\'", $enclosure], $value);
        
        if (is_int($key) || (is_numeric($key) && ctype_digit($key))) {
            $key = (int)$key;
            fwrite($file, str_repeat('    ', $level) . "{$key} => '{$value}',\n");
        } else {
            fwrite($file, str_repeat('    ', $level) . "'{$key}' => '{$value}',\n");
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
    public static function fputcsv($handle, $array, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = "\n")
    {
        $output = '';
        
        $count = count($array);
        foreach ($array as $i => $item) {
            $item = self::normalizeEOLs($item, $eol);
            
            if (Str::contains($item, $enclosure)) {
                $item = $enclosure . str_replace($enclosure, $escape . $enclosure, $item) . $enclosure;
            } elseif (Str::contains($item, [$delimiter, $eol])) {
                $item = $enclosure . $item . $enclosure;
            }
            
            $output .= $item;
            
            if ($i < $count - 1) {
                $output .= $delimiter;
            }
        }
        $output .= $eol;
        
        $output = mb_convert_encoding($output, 'UTF-8');
        
        return fwrite($handle, $output);
    }
    
    /**
     * Returns an array of duplicated values from an array of values
     * @param $values
     * @return array
     */
    public static function getDuplicateValues($values): array
    {
        $count = [];
        foreach ($values as $i => $value) {
            $count[$value][] = $i;
        }
        return collect($count)->filter(function ($items) {
            return count($items) > 1;
        })->toArray();
    }
    
    /**
     * Check if the value is a valid language code and suggest correct
     * @param string $code
     * @return bool|string
     */
    public static function isValidLanguageCode(string $code)
    {
        $languages = collect(require("Languages.php"));
        
        if ($languages->containsStrict($code)) {
            return true;
        }
        
        $fuse = new Fuse($languages->map(function ($item) {
            return ['code' => $item];
        })->toArray(), ['keys' => ['code']]);
        
        $search = $fuse->search($code);
        
        if (count($search) === 0) {
            return false;
        }
        
        return $search[0]['code'];
    }
    
    /**
     * Check if the value is a valid HTML
     * @param $string
     * @return bool
     */
    public static function isValidHTML($string): bool
    {
        try {
            $doc = new DOMDocument();
            $doc->loadHTML("<html><body>$string</body></html>");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Returns a collection of files by paths and patterns
     * @param array $paths
     * @param array $patterns
     * @return Collection|SplFileInfo[]
     */
    public static function findFiles(array $paths, array $patterns): Collection
    {
        $directories = array_map(static function ($dir) {
            return base_path($dir);
        }, $paths);
        
        $finder = new Finder();
        $files = $finder
            ->in($directories)
            ->name($patterns)
            ->files();
        return new Collection($files);
    }
    
    /**
     * Returns a collection of localization strings from a collection of files
     * @param Collection|SplFileInfo[] $files
     * @param array $functions
     * @return Collection
     */
    public static function parseStrings(Collection $files, array $functions): Collection
    {
        return $files
            ->map(function (SplFileInfo $file) use ($functions) {
                return Utils::getStrings($file, $functions);
            })
            ->flatMap(function ($collection) {
                return $collection->all();
            })->values();
    }
    
    /**
     * Returns a collection of localization strings from a file
     * @param SplFileInfo $file
     * @param array $functions
     * @return Collection
     */
    public static function getStrings(SplFileInfo $file, array $functions): Collection
    {
        $strings = collect();
        foreach ($functions as $function) {
            $content = self::normalizeEOLs($file->getContents());
            $regex = '/(' . $function . ')\(\h*[\'"](.+)[\'"]\h*[),]/U';
            if (preg_match_all($regex, $content, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[2] as $match) {
                    [$string, $offset] = $match;
                    $strings->push([
                        'string' => $string,
                        'filepath' => $file->getRealPath(),
                        'line' => substr_count(substr($content, 0, $offset), "\n") + 1,
                        'column' => $offset - strrpos(substr($content, 0, $offset), "\n"),
                    ]);
                }
            }
        }
        return $strings;
    }
    
    public static function msToHuman($inputMs): string
    {
        $msInASecond = 1000;
        $msInAMinute = 60 * $msInASecond;
        $msInAnHour = 60 * $msInAMinute;
        $msInADay = 24 * $msInAnHour;
        
        // Extract days
        $days = floor($inputMs / $msInADay);
        
        // Extract hours
        $hourSeconds = $inputMs % $msInADay;
        $hours = floor($hourSeconds / $msInAnHour);
        
        // Extract minutes
        $minuteSeconds = $hourSeconds % $msInAnHour;
        $minutes = floor($minuteSeconds / $msInAMinute);
        
        // Extract seconds
        $secondMilliseconds = $minuteSeconds % $msInAMinute;
        $seconds = floor($secondMilliseconds / $msInASecond);
        
        // Extract the remaining milliseconds
        $remainingMilliseconds = $secondMilliseconds % $msInASecond;
        $milliseconds = ceil($remainingMilliseconds);
        
        // Format and return
        $timeParts = [];
        $sections = [
            'day' => (int)$days,
            'hour' => (int)$hours,
            'minute' => (int)$minutes,
            'second' => (int)$seconds,
            'millisecond' => (int)$milliseconds,
        ];
        
        foreach ($sections as $name => $value) {
            if ($value > 0) {
                $timeParts[] = $value . ' ' . $name . ($value === 1 ? '' : 's');
            }
        }
        
        if (count($timeParts) === 0) {
            return '0 milliseconds';
        }
        
        return implode(', ', $timeParts);
    }
    
    public static function bytesToHuman($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
