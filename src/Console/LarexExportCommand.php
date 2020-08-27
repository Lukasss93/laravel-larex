<?php

namespace Lukasss93\Larex\Console;

use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Utils;

class LarexExportCommand extends Command
{
    protected $file = 'resources/lang/localization.csv';
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:export {--watch : Watch the CSV file from changes}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert the CSV file to Laravel lang files';
    
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if($this->option('watch')) {
            $this->watch();
            return;
        }
        
        $this->translate();
    }
    
    private function watch(): void
    {
        $this->warn("Watching the '$this->file' file...");
        
        $lastEditDate = null;
        Utils::forever(function() use (&$lastEditDate) {
            $currentEditDate = filemtime(base_path($this->file));
            clearstatcache();
            
            if($lastEditDate !== $currentEditDate) {
                $lastEditDate = $currentEditDate;
                $this->translate();
                $this->line('Waiting for changes...');
            }
            
            usleep(500 * 1000);
        });
    }
    
    private function translate(): void
    {
        $this->warn("Processing the '$this->file' file...");
        
        if(!File::exists(base_path($this->file))) {
            $this->error("The '$this->file' does not exists.");
            $this->line('Please create it with: php artisan larex:init');
            return;
        }
        
        $languages = [];
        $header = [];
        $columnsCount = 0;
        
        //file parsing
        $file = fopen(base_path($this->file), 'rb');
        $i = -1;
        while(($columns = fgetcsv($file, 0, ';')) !== false) {
            $i++;
            
            //get the header
            if($i === 0) {
                $header = $columns;
                $columnsCount = count($header);
                continue;
            }
            
            try {
                unset($group, $key);
                
                //get first two columns values
                [$group, $key] = $columns;
                
                if($key === '') {
                    throw new ErrorException();
                }
                
                for($j = 2; $j < $columnsCount; $j++) {
                    try {
                        if($columns[$j]!==''){
                            Arr::set($languages[$header[$j]][$group], $key, $columns[$j]);
                        }
                    } catch(ErrorException $e) {
                        $this->warn(
                            "[{$group}|{$key}] on line " . ($i + 1) .
                            ", column " . ($j + 1) .
                            " ({$header[$j]}) is not valid. It will be skipped."
                        );
                    }
                }
            } catch(ErrorException $ee) {
                $this->warn("Line " . ($i + 1) . " is not valid. It will be skipped.");
            }
        }
        fclose($file);
        
        if(count($languages) === 0) {
            $this->info('No entries found.');
            return;
        }
        
        //finally save the files
        foreach($languages as $language => $groups) {
            
            if(!File::exists(resource_path('lang/' . $language . '/'))){
                File::makeDirectory(resource_path('lang/' . $language . '/'));
            }
            
            foreach($groups as $group => $keys) {
                $write = fopen(resource_path('lang/' . $language . '/' . $group . '.php'), 'wb');
                fwrite($write, '<?php' . PHP_EOL . PHP_EOL . 'return [' . PHP_EOL . PHP_EOL);
                
                foreach($keys as $key => $value) {
                    Utils::writeKeyValue($key, $value, $write);
                }
                
                fwrite($write, PHP_EOL . '];' . PHP_EOL);
                
                fclose($write);
                $this->info("resources/lang/$language/$group.php created successfully.");
            }
        }
    }
}
