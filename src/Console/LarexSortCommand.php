<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Utils;

class LarexSortCommand extends Command
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
    protected $signature = 'larex:sort';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sort the CSV rows by group and key';

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
     * @return void
     */
    public function handle(): void
    {
        $this->warn('Sorting che CSV rows...');

        if (!File::exists(base_path($this->file))) {
            $this->error("The '{$this->file}' does not exists.");
            $this->line('Please create it with: php artisan larex:init');
            return;
        }

        [$header, $rows] = Utils::csvToCollection(base_path($this->file))->partition(function ($item, $key) {
            return $key === 0;
        });

        $content = collect([])
            ->merge($header)
            ->merge($rows->sortBy(function ($item) {
                return [$item[0], $item[1]];
            }))
            ->values();

        Utils::collectionToCsv($content, base_path($this->file));

        $this->info('Sorting completed.');
    }
}
