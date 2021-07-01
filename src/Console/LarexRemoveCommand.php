<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\CsvWriter;

class LarexRemoveCommand extends Command
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
    protected $signature = 'larex:remove {key}
                                         {--f|force : Disable confirmation}
                                         {--e|export=notset : Convert the CSV file to Laravel lang files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a key from CSV file';

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
        $key = $this->argument('key');
        $force = $this->option('force');

        if (!File::exists(base_path($this->file))) {
            $this->error("The '$this->file' does not exists.");
            $this->line('Please create it with: php artisan larex:init');

            return 1;
        }

        /** @var Collection $remove */
        /** @var Collection $keep */
        [$remove, $keep] = CsvReader::create(base_path($this->file))
            ->getRows()
            ->partition(fn ($item) => Str::is($key, "{$item['group']}.{$item['key']}"))
            ->collect();

        if ($remove->isEmpty()) {
            $this->warn('No strings found to remove.');

            return 0;
        }

        $this->warn($remove->count().' '.Str::plural('string', $remove->count()).' found to remove:');
        $remove->values()->each(function ($item, $key) use ($remove) {
            $char = $key < $remove->count() - 1 ? '├' : '└';
            $this->line("$char {$item['group']}.{$item['key']}");
        });

        if ($force || $this->confirm("Are you sure you want to delete {$remove->count()} ".Str::plural('string',
                    $remove->count()).'?', false)) {
            CsvWriter::create(base_path($this->file))->addRows($keep->toArray());

            $this->info('Removed successfully.');

            $export = $this->option('export');
            if ($export !== 'notset') {
                $export = $export === true ? null : $export;
                $this->newLine();
                $this->call(LarexExportCommand::class, ['exporter' => $export]);
            }

            return 0;
        }

        $this->line('<fg=red>Aborted.</>');

        return 0;
    }
}
