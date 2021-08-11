<?php

use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;

it('exports strings', function () {
    initFromStub('exporters.json-language.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en.json created successfully.')
        ->expectsOutput('resources/lang/it.json created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-language.base.output-en');

    expect(resource_path('lang/it.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-language.base.output-it');
});

it('exports strings with --watch option', function () {
    initFromStub('exporters.json-language.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang', '--watch' => true])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en.json created successfully.')
        ->expectsOutput('resources/lang/it.json created successfully.')
        ->expectsOutput('Waiting for changes...')
        ->assertExitCode(0);

    expect(resource_path('lang/en.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-language.base.output-en');

    expect(resource_path('lang/it.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-language.base.output-it');
});

it('exports strings with --include option', function () {
    initFromStub('exporters.json-language.include-exclude.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang', '--include' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/en.json created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-language.include-exclude.output-en');

    expect(resource_path('lang/it.json'))->not->toBeFile();
});

it('exports strings with --exclude option', function () {
    initFromStub('exporters.json-language.include-exclude.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang', '--exclude' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('resources/lang/it.json created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en.json'))->not->toBeFile();

    expect(resource_path('lang/it.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-language.include-exclude.output-it');

});

it('exports strings with warning', function () {
    initFromStub('exporters.json-language.warnings.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('Missing key name at line 3. The row will be skipped.')
        ->expectsOutput('app.zero at line 4, column 3 (en) is missing. It will be skipped.')
        ->expectsOutput('resources/lang/en.json created successfully.')
        ->expectsOutput('resources/lang/it.json created successfully.')
        ->assertExitCode(0);

    expect(resource_path('lang/en.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-language.warnings.output-en');

    expect(resource_path('lang/it.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-language.warnings.output-it');
});

it('exports strings with no entries', function () {
    $this->artisan(LarexInitCommand::class)->run();

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang'])
        ->expectsOutput(sprintf("Processing the '%s' file...", localizationPath(true)))
        ->expectsOutput('No entries exported.')
        ->assertExitCode(0);
});

