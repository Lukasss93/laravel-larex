<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Support\Utils;

class LarexInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larex:init {--base : Init the CSV file with default Laravel entries }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init the CSV file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $stub = 'default';

        if ($this->option('base')) {
            $stub = 'base';
        }

        if (File::exists(csv_path())) {
            $this->error(csv_path(true).' already exists.');

            return 1;
        }

        Utils::filePut(csv_path(), Utils::getStub($stub));

        $this->info(csv_path(true).' created successfully.');

        return 0;
    }
}
