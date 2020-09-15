<?php

namespace Lukasss93\Larex\Console;

use Illuminate\Console\Command;

class LarexCommand extends Command
{
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
        $this->warn('This command is deprecated. Please use the larax:export command.');

        if ($this->option('watch')) {
            $this->call('larex:export', ['--watch']);
            return;
        }

        $this->call('larex:export');
    }
}
