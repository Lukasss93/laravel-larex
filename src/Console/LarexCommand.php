<?php

namespace Lukasss93\Larex\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Utils;

class LarexCommand extends Command
{
    private $file = 'resources/lang/localization.csv';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex {--watch : Watch the CSV file from changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert the CSV file to Laravel lang files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->option('watch')) {
            $this->watch();
            return;
        }

        $this->translate();
    }

    private function watch(): void
    {
        $this->warn("Watching the '$this->file' file...");

        $lastEditDate = null;
        do {
            $currentEditDate = filemtime(base_path($this->file));
            clearstatcache();

            if ($lastEditDate !== $currentEditDate) {
                $lastEditDate = $currentEditDate;
                $this->translate();
                $this->line('Waiting for changes...');
            }

            usleep(500 * 1000);
        } while (true);
    }

    private function translate(): void
    {
        $this->warn("Processing the '$this->file' file...");

        if (!File::exists($this->file)) {
            $this->error("The '$this->file' does not exists.");
            $this->line('Please create it with: php artisan larex:init');
            return;
        }

        $languages = [];
        $header = [];
        $columnsCount = 0;

        //file parsing
        $file = fopen($this->file, 'rb');
        $i = -1;
        while(($row = fgetcsv($file)) !== false) {
            $i++;

            //get the row
            $columns = str_getcsv($row[0], ';');

            //get the header
            if ($i === 0) {
                $header = $columns;
                $columnsCount = count($header);
                continue;
            }

            //get first two columns values
            [$group, $key] = $columns;

            for ($j = 2; $j < $columnsCount; $j++) {
                try {
                    Arr::set($languages[$header[$j]][$group], $key, $columns[$j]);
                } catch (Exception $e) {
                    $this->warn("[{$group}|{$key}] on line " . ($i + 1) . ", column " . ($j + 1) . " is not valid. It will be skipped.");
                }
            }
        }
        fclose($file);

        if (count($languages) === 0) {
            $this->info("No entries found.");
            return;
        }

        //finally save the files
        foreach ($languages as $language => $groups) {
            //check lang directory exists
            if (!File::exists(resource_path('lang/' . $language))) {
                File::makeDirectory(resource_path('lang/' . $language));
            }

            foreach ($groups as $group => $keys) {
                $write = fopen(resource_path('lang/' . $language . '/' . $group . '.php'), 'wb');
                fwrite($write, "<?php\n\nreturn [\n\n");

                foreach ($keys as $key => $value) {
                    Utils::writeKeyValue($key, $value, $write);
                }

                fwrite($write, "\n];\n");

                fclose($write);
                $this->info("resources/lang/$language/$group.php created successfully.");
            }
        }
    }
}
