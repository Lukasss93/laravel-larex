<?php

use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;

it('exports strings', function () {
    initFromStub('exporters.laravel.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en/app.php created successfully.')
        ->expectsOutput('resources/lang/en/special.php created successfully.')
        ->expectsOutput('resources/lang/it/app.php created successfully.')
        ->expectsOutput('resources/lang/it/special.php created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-app');

    expect(resource_path('lang/en/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-special');

    expect(resource_path('lang/it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-app');

    expect(resource_path('lang/it/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-special');
});

it('exports strings with --watch option', function () {
    initFromStub('exporters.laravel.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel', '--watch' => true])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en/app.php created successfully.')
        ->expectsOutput('resources/lang/en/special.php created successfully.')
        ->expectsOutput('resources/lang/it/app.php created successfully.')
        ->expectsOutput('resources/lang/it/special.php created successfully.')
        ->expectsOutput('Waiting for changes...')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-app');

    expect(resource_path('lang/en/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-special');

    expect(resource_path('lang/it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-app');

    expect(resource_path('lang/it/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-special');
});

it('exports strings with --include option', function () {
    initFromStub('exporters.laravel.include-exclude.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel', '--include' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en/app.php created successfully.')
        ->expectsOutput('resources/lang/en/another.php created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.include-exclude.output-en-app');

    expect(resource_path('lang/en/another.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.include-exclude.output-en-another');

    expect(resource_path('lang/it/app.php'))->not->toBeFile();

    expect(resource_path('lang/it/another.php'))->not->toBeFile();
});

it('exports strings with --exclude option', function () {
    initFromStub('exporters.laravel.include-exclude.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel', '--exclude' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/it/app.php created successfully.')
        ->expectsOutput('resources/lang/it/another.php created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.php'))->not->toBeFile();

    expect(resource_path('lang/en/another.php'))->not->toBeFile();

    expect(resource_path('lang/it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.include-exclude.output-it-app');

    expect(resource_path('lang/it/another.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.include-exclude.output-it-another');
});

it('exports strings with warning', function () {
    initFromStub('exporters.laravel.warnings.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('Missing key name at line 4. The row will be skipped.')
        ->expectsOutput('app.second at line 5, column 3 (en) is missing. It will be skipped.')
        ->expectsOutput('resources/lang/en/app.php created successfully.')
        ->expectsOutput('resources/lang/it/app.php created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.warnings.output-en');

    expect(resource_path('lang/it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.warnings.output-it');
});

it('exports strings with no entries', function () {
    $this->artisan(LarexInitCommand::class)->run();

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('No entries exported.')
        ->assertExitCode(0);
});

it('exports strings with language code territory', function () {
    initFromStub('exporters.laravel.territory.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en_GB/app.php created successfully.')
        ->expectsOutput('resources/lang/it/app.php created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en_GB/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.territory.output-en_GB');

    expect(resource_path('lang/it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.territory.output-it');
});

it('exports strings with different eol', function () {
    config(['larex.eol' => "\n"]);

    initFromStub('exporters.laravel.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en/app.php created successfully.')
        ->expectsOutput('resources/lang/en/special.php created successfully.')
        ->expectsOutput('resources/lang/it/app.php created successfully.')
        ->expectsOutput('resources/lang/it/special.php created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-app');

    expect(resource_path('lang/en/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-en-special');

    expect(resource_path('lang/it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-app');

    expect(resource_path('lang/it/special.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.base.output-it-special');
});

it('exports strings trimming whitespaces in group and key', function () {
    initFromStub('exporters.laravel.spaces.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'laravel'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en/app.php created successfully.')
        ->expectsOutput('resources/lang/it/app.php created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.spaces.output-en');

    expect(resource_path('lang/it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.laravel.spaces.output-it');
});
