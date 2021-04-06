<?php

namespace Lukasss93\Larex\Tests;

use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;

class LarexExportTest extends TestCase
{
    public function test_export_with_missing_file(): void
    {
       $this->artisan(LarexExportCommand::class)
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init or php artisan larex:import')
            ->assertExitCode(1);
    }

    public function test_export_with_include_exclude(): void
    {
        $this->artisan(LarexInitCommand::class)->run();

        $this->artisan(LarexExportCommand::class, ['--include' => '', '--exclude'=>''])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("The --include and --exclude options can be used only one at a time.")
            ->assertExitCode(1);
    }

    public function test_export_with_missing_exporter(): void
    {
        $this->artisan(LarexInitCommand::class)->run();

        $this->artisan(LarexExportCommand::class, ['exporter' => 'foo'])
            ->expectsOutput("Exporter 'foo' not found.")
            ->expectsOutput('')
            ->expectsOutput('Available exporters:')
            ->expectsOutput('laravel - Export data from CSV to Laravel localization files')
            ->expectsOutput('json:lang - Export data from CSV to JSON by language')
            ->expectsOutput('json:group - Export data from CSV to JSON by group')
            ->expectsOutput('')
            ->assertExitCode(1);
    }

    public function test_export_with_invalid_exporter(): void
    {
        config(['larex.exporters.list.foo' => new class() {}]);

        $this->artisan(LarexInitCommand::class)->run();

        $this->artisan(LarexExportCommand::class, ['exporter' => 'foo'])
            ->expectsOutput("Exporter 'foo' must implements Lukasss93\Larex\Contracts\Exporter interface.")
            ->assertExitCode(1);
    }
}
