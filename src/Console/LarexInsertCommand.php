<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Support\Utils;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

class LarexInsertCommand extends Command
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
        $this->file = config('larex.csv.path');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (!File::exists(base_path($this->file))) {
            $this->error("The '{$this->file}' does not exists.");
            $this->line('Please create it with: php artisan larex:init');
            return 1;
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
                return "{$item}.";
            })
            ->toArray();

        //get available languages
        $languages = collect($header)->skip(2)->values();

        //initialize data
        $data = collect([]);

        //iterate until user confirm the inserted data
        do {
            do {
                $continue = true;

                //get group
                $group = '';
                do {
                    $group = trim($this->anticipate('Enter the group', $availableGroups, $data->get('group')));

                    if ($group === '') {
                        $this->error('Please enter a group!');
                    }
                } while ($group === '');
                $data->put('group', $group);

                //get key
                $key = '';
                do {
                    $key = trim($this->anticipate('Enter the key', $availableKeys, $data->get('key')));

                    if ($key === '') {
                        $this->error('Please enter a key!');
                    }
                } while ($key === '');
                $data->put('key', $key);

                if ($rows->where(0, $group)->where(1, $key)->isNotEmpty()) {
                    $continue = $this->askWithCompletion('<fg=red>The group/key pair already exists. Do you want to continue?</>', ['yes', 'no'], 'no') === 'yes';
                }
            } while (!$continue);


            foreach ($languages as $i => $language) {
                $count = $i + 1;
                $value = $this->ask(
                    "[{$count}/{$languages->count()}] Enter the value for [{$language}] language",
                    $data->get($language)
                );

                $data->put($language, $value);
            }

            $table = new Table($this->output);
            $tableRows = collect([]);
            $tableRows->push([new TableCell('<fg=yellow>Summary</>', ['colspan' => 2])]);
            $tableRows->push(new TableSeparator());

            $count = 0;
            foreach ($data as $i => $item) {
                $count++;
                $tableRows->push(["<info>{$i}</info>", $item]);

                if ($count < $data->count()) {
                    $tableRows->push(new TableSeparator());
                }
            }
            $table->setRows($tableRows->toArray());
            $table->render();

        } while ($this->askWithCompletion('Are you sure?', ['yes', 'no'], 'yes') !== 'yes');

        //append to csv
        $csv->push($data->values()->toArray());

        Utils::collectionToCsv($csv, base_path($this->file));
        $this->info('Item added successfully.');

        if ($this->option('export')) {
            $this->line('');
            $this->call('larex:export');
        }

        return 0;
    }
}
