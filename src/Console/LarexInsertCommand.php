<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\CsvWriter;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

class LarexInsertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:insert {--e|export=notset : Convert the CSV file to Laravel lang files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert a new record in the CSV';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (!File::exists(csv_path())) {
            $this->error(sprintf("The '%s' does not exists.", csv_path(true)));
            $this->line('Please create it with: php artisan larex:init');

            return 1;
        }

        //csv reader
        $reader = CsvReader::create(csv_path());

        //get csv header
        $header = $reader->getHeader();

        //get csv rows
        $rows = $reader->getRows()->collect();

        //get existing groups
        $availableGroups = $rows
            ->pluck('group')
            ->unique()
            ->toArray();

        //get existing keys
        $availableKeys = $rows
            ->pluck('key')
            ->unique()
            ->map(fn ($item) => "$item.")
            ->toArray();

        //get available languages
        $languages = $header->skip(2)->values();

        //initialize data
        /** @var Collection<int, string> $data */
        $data = collect([]);

        //iterate until user confirm the inserted data
        do {
            do {
                $continue = true;

                //get group
                do {
                    $group = trim($this->anticipate('Enter the group', $availableGroups, $data->get('group')));

                    if ($group === '') {
                        $this->error('Please enter a group!');
                    }
                } while ($group === '');
                $data->put('group', $group);

                //get key
                do {
                    $key = trim($this->anticipate('Enter the key', $availableKeys, $data->get('key')));

                    if ($key === '') {
                        $this->error('Please enter a key!');
                    }
                } while ($key === '');
                $data->put('key', $key);

                if ($rows->contains('group', $group) && $rows->contains('key', $key)) {
                    $continue = $this->askWithCompletion('<fg=red>The group/key pair already exists. Do you want to continue?</>', ['yes', 'no'], 'no') === 'yes';
                }
            } while (!$continue);

            foreach ($languages as $i => $language) {
                $count = $i + 1;
                $value = $this->ask(
                    "[$count/{$languages->count()}] Enter the value for [$language] language",
                    $data->get($language)
                );

                $data->put($language, $value);
            }

            $table = new Table($this->output);
            /** @var Collection<int,mixed> $tableRows */
            $tableRows = collect([]);
            $tableRows->push([new TableCell('<fg=yellow>Summary</>', ['colspan' => 2])]);
            $tableRows->push(new TableSeparator());

            $count = 0;
            foreach ($data as $i => $item) {
                $count++;
                $tableRows->push(["<info>$i</info>", $item]);

                if ($count < $data->count()) {
                    $tableRows->push(new TableSeparator());
                }
            }
            $table->setRows($tableRows->toArray());
            $table->render();
        } while ($this->askWithCompletion('Are you sure?', ['yes', 'no'], 'yes') !== 'yes');

        //append to csv
        $rows->push($data->toArray());

        CsvWriter::create(csv_path())
            ->addRows($rows->toArray());

        $this->info('Item added successfully.');

        $export = $this->option('export');
        if ($export !== 'notset') {
            $export = $export === true ? null : $export;
            $this->line('');
            $this->call(LarexExportCommand::class, ['exporter' => $export]);
        }

        return 0;
    }
}
