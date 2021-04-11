<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\CsvWriter;

class LarexSortCommand extends Command
{
    /**
     * Localization file path.
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
     * @return int
     */
    public function handle(): int
    {
        $this->warn('Sorting che CSV rows...');

        if (!File::exists(base_path($this->file))) {
            $this->error("The '{$this->file}' does not exists.");
            $this->line('Please create it with: php artisan larex:init');

            return 1;
        }

        $content = CsvReader::create(base_path($this->file))
            ->getRows()
            ->sortBy(fn ($item) => [$item['group'], $item['key']])
            ->collect();

        CsvWriter::create(base_path($this->file))
            ->addRows($content->toArray());

        $this->info('Sorting completed.');

        return 0;
    }
}
