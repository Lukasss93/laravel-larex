<?php

namespace Lukasss93\Larex\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Exceptions\MissingValueException;
use Lukasss93\Larex\Utils;

class LarexExportCommand extends Command
{
    /**
     * Localization file path
     *
     * @var string
     */
    protected $file;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:export
                            {--watch : Watch the CSV file from changes}
                            {--include= : Languages allowed to export in the application}
                            {--exclude= : Languages not allowed to export in the application}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert the CSV file to Laravel lang files';
    
    /**
     * Create a new console command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->file = config('larex.csv.path');
    }
    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('watch')) {
            return $this->watch();
        }
        
        return $this->translate();
    }
    
    private function watch(): int
    {
        $this->warn("Watching the '$this->file' file...");
        
        $lastEditDate = null;
        Utils::forever(function () use (&$lastEditDate) {
            $currentEditDate = filemtime(base_path($this->file));
            clearstatcache();
            
            if ($lastEditDate !== $currentEditDate) {
                $lastEditDate = $currentEditDate;
                $this->translate();
                $this->line('Waiting for changes...');
            }
            
            usleep(500 * 1000);
        });
        
        return 0;
    }
    
    private function translate(): int
    {
        $this->warn("Processing the '$this->file' file...");
        
        if (!File::exists(base_path($this->file))) {
            $this->error("The '$this->file' does not exists.");
            $this->line('Please create it with: php artisan larex:init');
            return 1;
        }
        
        if ($this->option('include') !== null && $this->option('exclude') !== null) {
            $this->error('The --include and --exclude options can be used only one at a time.');
            return 1;
        }
        
        $languages = [];
        
        //file parsing
        $csv = Utils::csvToCollection(base_path($this->file))->mapInto(Collection::class);
        $header = $csv->get(0);
        $columnsCount = $header->count();
        $rows = $csv->skip(1);
        foreach ($rows as $i => $columns) {
            $line = $i + 1;
            
            //check if row is blank
            if ($columns->count() <= 1 && $columns->get(0) === null) {
                $this->warn("Invalid row at line {$line}. The row will be skipped.");
                continue;
            }
            
            //get first two columns values
            [$group, $key] = $columns;
            
            //check if key is filled
            if ($key === '') {
                $this->warn("Missing key name at line {$line}. The row will be skipped.");
                continue;
            }
            
            //loop columns
            for ($j = 2; $j < $columnsCount; $j++) {
                $item = $columns->get($j) ?? '';
                $column = $j + 1;
                try {
                    if ($item !== '') {
                        Arr::set($languages[$header[$j]][$group], $key, $item);
                    } else if ($this->option('verbose')) {
                        throw new MissingValueException("Missing value in {$header[$j]} column.");
                    }
                } catch (MissingValueException $e) {
                    $this->warn(
                        "{$group}.{$key} at line {$line}, column {$column} ({$header[$j]}) " .
                        "is missing. It will be skipped."
                    );
                }
            }
            
        }
        
        if ($this->option('include') !== null) {
            $allowed = explode(',', $this->option('include'));
            
            $languages = array_filter($languages, function ($value, $key) use ($allowed) {
                return in_array($key, $allowed, true);
            }, ARRAY_FILTER_USE_BOTH);
        } else {
            if ($this->option('exclude') !== null) {
                $allowed = explode(',', $this->option('exclude'));
                
                $languages = array_filter($languages, function ($value, $key) use ($allowed) {
                    return !in_array($key, $allowed, true);
                }, ARRAY_FILTER_USE_BOTH);
            }
        }
        
        if (count($languages) === 0) {
            $this->info('No entries found.');
            return 2;
        }
        
        //finally save the files
        foreach ($languages as $language => $groups) {
            if (!File::exists(resource_path('lang/' . $language . '/'))) {
                File::makeDirectory(resource_path('lang/' . $language . '/'));
            }
            
            foreach ($groups as $group => $keys) {
                $write = fopen(resource_path('lang/' . $language . '/' . $group . '.php'), 'wb');
                fwrite($write, '<?php' . PHP_EOL . PHP_EOL . 'return [' . PHP_EOL . PHP_EOL);
                
                foreach ($keys as $key => $value) {
                    Utils::writeKeyValue($key, $value, $write);
                }
                
                fwrite($write, PHP_EOL . '];' . PHP_EOL);
                
                fclose($write);
                $this->info("resources/lang/$language/$group.php created successfully.");
            }
        }
        
        return 0;
    }
}
