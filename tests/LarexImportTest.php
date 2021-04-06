<?php

namespace Lukasss93\Larex\Tests;

use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;

class LarexImportTest extends TestCase
{
    public function test_import_with_missing_importer(): void
    {
        $this->artisan(LarexImportCommand::class, ['importer' => 'foo'])
            ->expectsOutput("Importer 'foo' not found.")
            ->expectsOutput('')
            ->expectsOutput('Available importers:')
            ->expectsOutput('laravel - Import data from Laravel localization files to CSV')
            ->expectsOutput('json:lang - Import data from JSON by language to CSV')
            ->expectsOutput('json:group - Import data from JSON by group to CSV')
            ->expectsOutput('')
            ->assertExitCode(1);
    }

    public function test_import_with_invalid_importer(): void
    {
        config(['larex.importers.list.foo' => new class() {
        }]);

        $this->artisan(LarexImportCommand::class, ['importer' => 'foo'])
            ->expectsOutput("Importer 'foo' must implements Lukasss93\Larex\Contracts\Importer interface.")
            ->assertExitCode(1);
    }

    public function test_import_with_existing_file(): void
    {
        $this->artisan(LarexInitCommand::class)->run();

        $this->artisan(LarexImportCommand::class)
            ->expectsOutput(sprintf("The '%s' already exists.", config('larex.csv.path')))
            ->assertExitCode(1);
    }

    public function test_import_with_existing_file_and_force(): void
    {
        $this->artisan(LarexInitCommand::class)->run();

        $this->artisan(LarexImportCommand::class, ['--force' => true])
            ->assertExitCode(0);
    }
}
