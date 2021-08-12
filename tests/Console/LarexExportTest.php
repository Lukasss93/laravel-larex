<?php

use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;

it('does not export strings due to missing localization file', function () {
    $this->artisan(LarexExportCommand::class)
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput(sprintf("The '%s' does not exists.", localizationPath(true)))
        ->expectsOutput('Please create it with: php artisan larex:init or php artisan larex:import')
        ->assertExitCode(1);
});

it('does not export strings due to both filled --include and --exclude options', function () {
    $this->artisan(LarexInitCommand::class)->run();

    $this->artisan(LarexExportCommand::class, ['--include' => '', '--exclude' => ''])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('The --include and --exclude options can be used only one at a time.')
        ->assertExitCode(1);
});

it('does not export strings due to missing exporter', function () {
    $this->artisan(LarexInitCommand::class);

    $this->artisan(LarexExportCommand::class, ['exporter' => 'foo'])
        ->expectsOutput("Exporter 'foo' not found.")
        ->expectsOutput('')
        ->expectsOutput('Available exporters:')
        ->expectsOutput('laravel - Export data from CSV to Laravel localization files')
        ->expectsOutput('json:lang - Export data from CSV to JSON by language')
        ->expectsOutput('json:group - Export data from CSV to JSON by group')
        ->expectsOutput('')
        ->assertExitCode(1);
});

it('does not export strings due to invalid exporter', function () {
    config([
        'larex.exporters.list.foo' => new class()
        {
        },
    ]);

    $this->artisan(LarexInitCommand::class);

    $this->artisan(LarexExportCommand::class, ['exporter' => 'foo'])
        ->expectsOutput("Exporter 'foo' must implements Lukasss93\Larex\Contracts\Exporter interface.")
        ->assertExitCode(1);
});
