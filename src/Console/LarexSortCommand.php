<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Lukasss93\Larex\Utils;

class LarexSortCommand extends Command
{
    protected $file = 'resources' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'localization.csv';
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:sort';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sort the CSV rows by group and key';
    
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->warn('Sorting che CSV rows...');
        
        [$header, $rows] = Utils::csvToCollection(base_path($this->file))->partition(function($item, $key) {
            return $key === 0;
        });
        
        $content = collect([])
            ->merge($header)
            ->merge($rows->sortBy(function($item) {
                return [$item[0], $item[1]];
            }))
            ->values();
        
        Utils::collectionToCsv($content, base_path($this->file));
        
        $this->info('Sorting completed.');
    }
}
