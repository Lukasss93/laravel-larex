<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\Utils;

class LarexLintCommand extends Command
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
    protected $signature = 'larex:lint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lint the CSV file';

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
            $this->error("The '$this->file' does not exists.");
            $this->line('Please create it with: php artisan larex:init');
            return 1;
        }

        //get linters
        /** @var Linter[] $linters */
        $linters = config('larex.linters');

        $status = collect([]);

        $timeStart = microtime(true);
        $memoryStart = memory_get_usage();

        foreach ($linters as $linter) {
            $instance = new $linter;

            if ($instance instanceof Linter) {
                $this->warn($instance->description());

                try {
                    $instance->handle(CsvReader::create(base_path($this->file)));

                    $this->line("<bg=green> PASS </> <fg=green>Lint passed successfully.</>");
                    $status->push(true);
                } catch (LintException $e) {
                    $this->line("<bg=red> FAIL </> <fg=red>{$e->getMessage()}</>");
                    $status->push(false);
                    $errors = $e->getErrors();
                    $totalErrors = count($errors);
                    foreach ($errors as $i => $error) {
                        $char = $i < $totalErrors - 1 ? '├' : '└';
                        $this->line("<fg=red>{$char} {$error}</>");
                    }
                }
                $this->line('');
            }
        }

        $this->line('........................');
        $this->line('');

        $time = Utils::msToHuman((int)((microtime(true) - $timeStart) * 1000));
        $memory = Utils::bytesToHuman(memory_get_usage() - $memoryStart);

        $this->line("Time: <fg=green>{$time}</>    Memory: <fg=green>{$memory}</>");
        $this->line('');

        $total = $status->count();
        $pass = $status->filter()->count();
        $fail = $status->reject()->count();

        if ($total === 0) {
            //no linters processed
            $this->line("<bg=yellow;fg=black>No linters executed!</>");
            return -1;
        }

        if ($total !== $pass) {
            //some test fails
            $this->line('<bg=red>FAILURES!</>');
            $this->line("<bg=red>Linters: {$total}, Failures: {$fail}</>");
            return 1;
        }

        //all test passes
        $linters = Str::plural('linter', $pass);
        $this->line("<bg=green>OK ({$pass} {$linters})</>");
        return 0;
    }
}
