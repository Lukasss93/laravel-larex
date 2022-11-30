<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Lukasss93\Larex\Contracts\Importer;
use Lukasss93\Larex\Exceptions\ImportException;
use Lukasss93\Larex\Support\CsvWriter;
use Throwable;

class LarexImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:import
                            {importer? : Importer}
                            {--f|force : Overwrite CSV file if already exists}
                            {--include= : Languages allowed to import in the CSV}
                            {--exclude= : Languages not allowed to import in the CSV}
                            {--skip-source-reordering : Skip source reordering}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import entries into CSV file';

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
        if (!$this->option('force') && File::exists(csv_path())) {
            $this->error(sprintf("The '%s' already exists.", csv_path(true)));

            return 1;
        }

        //check concurrent options
        if ($this->option('include') !== null && $this->option('exclude') !== null) {
            $this->error('The --include and --exclude options can be used only one at a time.');

            return 1;
        }

        $this->warn('Importing entries...');

        try {
            //call the importer
            $items = $importer->handle($this);
        } catch (ImportException $e) {
            $this->error($e->getMessage());

            return 1;
        }

        //check no data
        if ($items->isEmpty()) {
            $this->warn('No data found to import.');

            return 0;
        }

        try {
            //validate items structure
            self::validateCollection($items);
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return 1;
        }

        //write csv
        CsvWriter::create(csv_path())
            ->addRows($items->toArray());

        //set source languages
        if (!$this->option('skip-source-reordering')) {
            $this->callSilently(LarexLangOrderCommand::class, ['from' => config('larex.source_language'), 'to' => 1]);
        }

        $this->info('Data imported successfully.');

        return 0;
    }

    protected static function validateCollection(Collection $rows): void
    {
        $compare = null;
        foreach ($rows as $i => $columns) {
            if (!is_array($columns)) {
                throw new InvalidArgumentException("The item must be an array at index $i.");
            }

            $keys = collect($columns)->keys();

            if ($keys->get(0) !== 'group') {
                throw new InvalidArgumentException("The first key name of the item must be 'group' at index $i.");
            }

            if ($keys->get(1) !== 'key') {
                throw new InvalidArgumentException("The first key name of the item must be 'key' at index $i.");
            }

            if ($keys->count() <= 2) {
                throw new InvalidArgumentException("There must be at least one language code at index $i.");
            }

            if ($compare === null) {
                $compare = $keys;
                continue;
            }

            if ($keys->count() !== $compare->count()) {
                throw new InvalidArgumentException("All items in the collection must be the same length at index $i.");
            }

            foreach ($keys->skip(2) as $j => $key) {
                if ($key !== $compare->get($j)) {
                    throw new InvalidArgumentException("All items in the collection must have the same keys values in the same position at index $i.");
                }
            }
        }
    }
}
