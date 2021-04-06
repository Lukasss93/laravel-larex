<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Contracts\Importer;
use Lukasss93\Larex\Support\Utils;

class LarexImportCommand extends Command
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
    protected $signature = 'larex:import
                            {importer? : Importer}
                            {--f|force : Overwrite CSV file if already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import entries into CSV file';

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
        //get the importer name
        $importerKey = $this->argument('importer') ?? config('larex.importers.default');
        $importers = config('larex.importers.list');

        //check if importer exists
        if (!array_key_exists($importerKey, $importers)) {
            $this->error("Importer '$importerKey' not found.");
            $this->line('');
            $this->info('Available importers:');
            foreach ($importers as $key => $importer) {
                $this->line("<fg=yellow>$key</> - {$importer::description()}");
            }
            $this->line('');
            return 1;
        }

        //initialize importer
        $importer = new $importers[$importerKey]();

        //check if importer is valid
        if (!($importer instanceof Importer)) {
            $this->error(sprintf("Importer '%s' must implements %s interface.", $importerKey, Importer::class));
            return 1;
        }

        //check file exists
        if (!$this->option('force') && File::exists(base_path($this->file))) {
            $this->error("The '{$this->file}' already exists.");
            return 1;
        }

        $this->warn('Importing entries...');

        //call the importer
        $items = $importer->handle($this);

        //validate items structure
        //TODO

        //write csv
        Utils::collectionToCsv($items, base_path($this->file));

        $this->info('Data imported successfully.');

        return 0;
    }
}
