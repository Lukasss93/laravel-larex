<?php

use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;

it('exports strings', function () {
    initFromStub('exporters.json-groups.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en/app.json created successfully.')
        ->expectsOutput('resources/lang/en/special.json created successfully.')
        ->expectsOutput('resources/lang/it/app.json created successfully.')
        ->expectsOutput('resources/lang/it/special.json created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-app');

    expect(resource_path('lang/en/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-special');

    expect(resource_path('lang/it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-app');

    expect(resource_path('lang/it/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-special');
});

it('exports strings with --watch option', function () {
    initFromStub('exporters.json-groups.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group', '--watch' => true])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en/app.json created successfully.')
        ->expectsOutput('resources/lang/en/special.json created successfully.')
        ->expectsOutput('resources/lang/it/app.json created successfully.')
        ->expectsOutput('resources/lang/it/special.json created successfully.')
        ->expectsOutput('Waiting for changes...')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-app');

    expect(resource_path('lang/en/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-special');

    expect(resource_path('lang/it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-app');

    expect(resource_path('lang/it/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-special');
});

it('exports strings with --include option', function () {
    initFromStub('exporters.json-groups.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group', '--include' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en/app.json created successfully.')
        ->expectsOutput('resources/lang/en/special.json created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-app');

    expect(resource_path('lang/en/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-special');

    expect(resource_path('lang/it/app.json'))->not->toBeFile();

    expect(resource_path('lang/it/special.json'))->not->toBeFile();
});

it('exports strings with --exclude option', function () {
    initFromStub('exporters.json-groups.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group', '--exclude' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/it/app.json created successfully.')
        ->expectsOutput('resources/lang/it/special.json created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.json'))->not->toBeFile();

    expect(resource_path('lang/en/special.json'))->not->toBeFile();

    expect(resource_path('lang/it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-app');

    expect(resource_path('lang/it/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-special');
});

it('exports strings with warning', function () {
    initFromStub('exporters.json-groups.warnings.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('Missing key name at line 4. The row will be skipped.')
        ->expectsOutput('app.second at line 5, column 3 (en) is missing. It will be skipped.')
        ->expectsOutput('resources/lang/en/app.json created successfully.')
        ->expectsOutput('resources/lang/it/app.json created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.warnings.output-en-app');

    expect(resource_path('lang/it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.warnings.output-it-app');
});

it('exports strings with no entries', function () {
    $this->artisan(LarexInitCommand::class);

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('No entries exported.')
        ->assertExitCode(0);
});

it('exports strings trimming whitespaces in group and key', function () {
    initFromStub('exporters.json-groups.spaces.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en/app.json created successfully.')
        ->expectsOutput('resources/lang/it/app.json created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.spaces.output-en-app');

    expect(resource_path('lang/it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.spaces.output-it-app');
});
