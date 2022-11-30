<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\CsvWriter;
use RuntimeException;
use Throwable;

class LarexLangOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:lang:order {from : Target language code or position (starting from 1) } 
                                             {to : Destination language code or position (starting from 1) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order a language column from the CSV file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        /** @var string $from */
        $from = $this->argument('from');

        /** @var string $to */
        $to = $this->argument('to');

        //check if csv file exists
        if (!File::exists(csv_path())) {
            $this->error(sprintf("The '%s' does not exists.", csv_path(true)));
            $this->line('Please create it with: php artisan larex:init');

            return 1;
        }

        //read CSV file
        $csvReader = CsvReader::create(csv_path());

        //get available languages
        /** @var Collection $languages */
        $languages = $csvReader->getHeader()->skip(2)->values();

        $content = $csvReader->getRows()->collect();

        try {
            //get source and destination indexes
            $sourceIndex = $this->getLanguageIndex($from, $languages, 'source');
            $destinationIndex = $this->getLanguageIndex($to, $languages, 'destination');

            //check if index are the same
            if ($sourceIndex === $destinationIndex) {
                throw new RuntimeException('The source and destination languages are the same.');
            }

            //pull source language
            $pulledLanguage = $languages->pull($sourceIndex);

            //insert source language in destination position
            $languages = $languages->insertAt($destinationIndex, $pulledLanguage);

            //write csv
            $csvWriter = CsvWriter::create(csv_path());
            $content->each(function (Collection $row) use ($csvWriter, $languages) {
                $item = collect([]);
                $item->put('group', $row->get('group'));
                $item->put('key', $row->get('key'));
                $languages->each(fn ($language) => $item->put($language, $row->get($language)));
                $csvWriter->addRow($item->toArray());
            });

            $this->info('Done.');
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }

    protected function getLanguageIndex(string $language, Collection $languages, string $type): int
    {
        if (is_numeric($language)) {
            $position = (int)$language;
            if (!$languages->has($position - 1)) {
                throw new RuntimeException(sprintf('The %s language (%s) is not valid.', $type, $language));
            }

            return $position - 1;
        }

        $index = $languages->search($language);

        if ($index === false) {
            throw new RuntimeException(sprintf('The %s language (%s) is not valid.', $type, $language));
        }

        return $index;
    }
}
