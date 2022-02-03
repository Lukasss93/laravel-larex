<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Lukasss93\Larex\Support\CsvReader;
use Symfony\Component\Console\Helper\Table;

class LarexFindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:find {value} {--w|width=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find between strings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $width = (int) $this->option('width');

        $value = Str::lower($this->argument('value'));
        $find = Str::of($value)->explode('.');
        $findGroup = $find->first();
        $findKey = $find->skip(1)->implode('.');

        if (!File::exists(csv_path())) {
            $this->error(sprintf("The '%s' does not exists.", csv_path(true)));
            $this->line('Please create it with: php artisan larex:init or php artisan larex:import');

            return 1;
        }

        $reader = CsvReader::create(csv_path());

        //get headers
        $headers = $reader
            ->getHeader()
            ->take(3);

        //get rows
        $result = $reader
            ->getRows()
            ->filter(fn ($item) => Str::contains(Str::lower("{$item['group']}.{$item['key']}"), $value))
            ->map(fn ($item) => [
                str_replace($findGroup, "<fg=yellow>$findGroup</>", $item['group']),
                str_replace([$findGroup, $findKey], ["<fg=yellow>$findGroup</>", "<fg=yellow>$findKey</>"],
                    $item['key']),
                $item[$headers->get(2)],
            ])
            ->collect();

        if ($result->isEmpty()) {
            $this->line('<fg=red>No string found.</>');

            return 0;
        }

        $table = new Table($this->output);
        $table
            ->setHeaders($headers->toArray())
            ->setRows($result->toArray())
            ->setColumnMaxWidth(2, $width)
            ->render();

        return 0;
    }
}
