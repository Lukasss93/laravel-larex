<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Utils;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

class LarexInsertCommand extends Command
{
    protected $file;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:insert {--e|export : Convert the CSV file to Laravel lang files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert a new record in the CSV';

    /**
     * Create a new console command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->file = config('larex.path');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (!File::exists(base_path($this->file))) {
            $this->error("The '$this->file' does not exists.");
            $this->line('Please create it with: php artisan larex:init');
            return;
        }

        //get the csv
        $csv = Utils::csvToCollection(base_path($this->file));

        //get csv header
        $header = $csv->get(0);

        //get csv rows
        $rows = $csv->skip(1);

        //get existing groups
        $availableGroups = $rows
            ->pluck(0)
            ->unique()
            ->toArray();

        //get existing keys
        $availableKeys = $rows
            ->pluck(1)
            ->unique()
            ->map(function ($item) {
                return "$item.";
            })
            ->toArray();

        //get available languages
        $languages = collect($header)->skip(2)->values();

        //initialize data
        $data = collect([]);

        //get group
        $data->put('group', $this->anticipate('Enter the group', $availableGroups));

        //get key
        $data->put('key', $this->anticipate('Enter the key', $availableKeys));

        foreach ($languages as $i => $language) {
            $n = $i + 1;
            $value = $this->ask("[{$n}/{$languages->count()}] Enter the value for [$language] language");

            $data->put($language, $value);
        }

        //append to csv
        $csv->push($data->values()->toArray());

        $table = new Table($this->output);
        $tableRows = collect([]);
        $tableRows->push([new TableCell('<fg=yellow>Summary</>', ['colspan' => 2])]);
        $tableRows->push(new TableSeparator());

        $n = 0;
        foreach ($data as $i => $item) {
            $n++;
            $tableRows->push(["<info>$i</info>", $item]);

            if ($n < $data->count()) {
                $tableRows->push(new TableSeparator());
            }
        }
        $table->setRows($tableRows->toArray());
        $table->render();

        Utils::collectionToCsv($csv, base_path($this->file));
        $this->info('Item added successfully.');

        if ($this->option('export')) {
            $this->line('');
            $this->call('larex:export');
        }
    }
}
