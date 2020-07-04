<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Lukasss93\Larex\Utils;

class LarexSortCommand extends Command
{
    private const FILE = 'resources/lang/localization.csv';

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

        [$header, $rows] = Utils::csvToCollection(self::FILE)->partition(function($item, $key) {
            return $key === 0;
        });

        $content = collect([])
            ->merge($header)
            ->merge($rows->sortBy(function($item) {
                return [$item[0], $item[1]];
            }))
            ->values();

        Utils::collectionToCsv($content, self::FILE);

        $this->info('Sorting completed.');
    }
}
