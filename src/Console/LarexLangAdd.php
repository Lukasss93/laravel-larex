<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\CsvWriter;
use Lukasss93\Larex\Support\Utils;

class LarexLangAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:lang:add {code : Language code }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new language to the CSV file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        //get code parameter
        $code = $this->argument('code');

        //check if code is valid
        $suggest = Utils::isValidLanguageCode($code);

        if ($suggest === false) {
            $this->error("Invalid language code ($code)");

            return 1;
        }

        if (is_string($suggest)) {
            $this->warn("Language code is not valid ($code). Did you mean: $suggest?");

            return 1;
        }

        //get CSV file
        $content = CsvReader::create(csv_path())
            ->getRows()
            ->collect()
            ->map(function (Collection $row) use ($code) {
                return $row->put($code, null);
            });

        //write new language code to CSV file
        CsvWriter::create(csv_path())
            ->addRows($content->toArray());

        $this->info("Added language column: \"$code\"");

        return 0;
    }
}
