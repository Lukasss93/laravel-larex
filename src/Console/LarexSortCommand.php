<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\CsvWriter;

class LarexSortCommand extends Command
{
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
     * @return int
     */
    public function handle(): int
    {
        $this->warn('Sorting che CSV rows...');

        if (!File::exists(csv_path())) {
            $this->error(sprintf("The '%s' does not exists.", csv_path(true)));
            $this->line('Please create it with: php artisan larex:init');

            return 1;
        }

        $content = CsvReader::create(csv_path())
            ->getRows()
            ->sortBy(fn ($item) => [$item['group'], $item['key']])
            ->collect();

        CsvWriter::create(csv_path())
            ->addRows($content->toArray());

        $this->info('Sorting completed.');

        return 0;
    }
}
