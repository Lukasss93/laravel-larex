<?php

use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;

it('exports strings', function () {
    initFromStub('exporters.laravel.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/special.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/special.php')))
        ->assertExitCode(0);

    expect(lang_path('en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-app');

    expect(lang_path('en/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-special');

    expect(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-app');

    expect(lang_path('it/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-special');
});

it('exports strings with --watch option', function () {
    initFromStub('exporters.laravel.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel', '--watch' => true])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/special.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/special.php')))
        ->expectsOutput('Waiting for changes...')
        ->assertExitCode(0);

    expect(lang_path('en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-app');

    expect(lang_path('en/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-special');

    expect(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-app');

    expect(lang_path('it/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-special');
});

it('exports strings with --include option', function () {
    initFromStub('exporters.laravel.include-exclude.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel', '--include' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/another.php')))
        ->assertExitCode(0);

    expect(lang_path('en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.include-exclude.output-en-app');

    expect(lang_path('en/another.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.include-exclude.output-en-another');

    expect(lang_path('it/app.php'))->not->toBeFile();

    expect(lang_path('it/another.php'))->not->toBeFile();
});

it('exports strings with --exclude option', function () {
    initFromStub('exporters.laravel.include-exclude.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel', '--exclude' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(lang_rpath('it/app.php').' created successfully.')
        ->expectsOutput(lang_rpath('it/another.php').' created successfully.')
        ->assertExitCode(0);

    expect(lang_path('en/app.php'))->not->toBeFile();

    expect(lang_path('en/another.php'))->not->toBeFile();

    expect(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.include-exclude.output-it-app');

    expect(lang_path('it/another.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.include-exclude.output-it-another');
});

it('exports strings with warning', function () {
    initFromStub('exporters.laravel.warnings.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput('Missing key name at line 4. The row will be skipped.')
        ->expectsOutput('app.second at line 5, column 3 (en) is missing. It will be skipped.')
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.php')))
        ->assertExitCode(0);

    expect(lang_path('en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.warnings.output-en');

    expect(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.warnings.output-it');
});

it('exports strings with no entries', function () {
    $this->artisan(LarexInitCommand::class)->run();

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput('No entries exported.')
        ->assertExitCode(0);
});

it('exports strings with language code territory', function () {
    initFromStub('exporters.laravel.territory.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en_GB/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.php')))
        ->assertExitCode(0);

    expect(lang_path('en_GB/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.territory.output-en_GB');

    expect(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.territory.output-it');
});

it('exports strings with different eol', function () {
    config(['larex.eol' => "\n"]);

    initFromStub('exporters.laravel.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/special.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/special.php')))
        ->assertExitCode(0);

    expect(lang_path('en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-app');

    expect(lang_path('en/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-special');

    expect(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-app');

    expect(lang_path('it/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-special');
});

it('exports strings trimming whitespaces in group and key', function () {
    initFromStub('exporters.laravel.spaces.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.php')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.php')))
        ->assertExitCode(0);

    expect(lang_path('en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.spaces.output-en');

    expect(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.spaces.output-it');
});
