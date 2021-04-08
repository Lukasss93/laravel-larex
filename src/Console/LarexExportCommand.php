<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Contracts\Exporter;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\Utils;

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
                            {exporter? : Exporter}
                            {--watch : Watch the CSV file from changes}
                            {--include= : Languages allowed to export in the application}
                            {--exclude= : Languages not allowed to export in the application}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export entries from CSV file';

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

        //check if csv file exists
        if (!File::exists(base_path($this->file))) {
            $this->error("The '$this->file' does not exists.");
            $this->line('Please create it with: php artisan larex:init or php artisan larex:import');
            return 1;
        }

        //check concurrent options
        if ($this->option('include') !== null && $this->option('exclude') !== null) {
            $this->error('The --include and --exclude options can be used only one at a time.');
            return 1;
        }

        //csv reader
        $reader = CsvReader::create(base_path($this->file));

        //get the exporter name
        $exporterKey = $this->argument('exporter') ?? config('larex.exporters.default');
        $exporters = config('larex.exporters.list');

        //check if exporter exists
        if (!array_key_exists($exporterKey, $exporters)) {
            $this->error("Exporter '$exporterKey' not found.");
            $this->line('');
            $this->info('Available exporters:');
            foreach ($exporters as $key => $exporter) {
                $description = $exporter::description();
                $this->line("<fg=yellow>$key</> - $description");
            }
            $this->line('');
            return 1;
        }

        //initialize exporter
        $exporter = new $exporters[$exporterKey]();

        //check if exporter is valid
        if (!($exporter instanceof Exporter)) {
            $this->error(sprintf("Exporter '%s' must implements %s interface.", $exporterKey, Exporter::class));
            return 1;
        }

        //call the exporter
        return $exporter->handle($this, $reader);
    }
}
