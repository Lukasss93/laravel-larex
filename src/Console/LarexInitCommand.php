<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Utils;

class LarexInitCommand extends Command
{
    protected $file;

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
        $stub = 'default';

        if ($this->option('base')) {
            $stub = 'base';
        }

        if (File::exists(base_path($this->file))) {
            $this->error($this->file . ' already exists.');
            return;
        }

        File::put(base_path($this->file), Utils::getStub($stub));

        $this->info($this->file . ' created successfully.');
    }
}
