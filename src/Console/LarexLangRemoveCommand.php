<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\CsvWriter;

class LarexLangRemoveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:lang:remove {code : Language code }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a language column from the CSV file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        //get code parameter
        $code = $this->argument('code');

        //get CSV file
        $csv = CsvReader::create(csv_path());

        //get languages
        $languages = $csv->getHeader()->skip(2)->values();

        //check if language code is present
        if (!$languages->contains($code)) {
            $this->error("The language code \"$code\" is not present in the CSV file.");

            return 1;
        }

        //remove language code
        $content = $csv
            ->getRows()
            ->collect()
            ->map(function (Collection $row) use ($code) {
                return $row->forget($code);
            });

        //write new language code to CSV file
        CsvWriter::create(csv_path())
            ->addRows($content->toArray());

        $this->info("The language code \"$code\" has been removed from the CSV file.");

        return 0;
    }
}
