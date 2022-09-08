<?php

use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;

it('exports strings', function () {
    initFromStub('exporters.json-groups.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/special.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/special.json')))
        ->assertExitCode(0);

    expect(lang_path('en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-app');

    expect(lang_path('en/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-special');

    expect(lang_path('it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-app');

    expect(lang_path('it/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-special');
});

it('exports strings with --watch option', function () {
    initFromStub('exporters.json-groups.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group', '--watch' => true])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/special.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/special.json')))
        ->expectsOutput('Waiting for changes...')
        ->assertExitCode(0);

    expect(lang_path('en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-app');

    expect(lang_path('en/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-special');

    expect(lang_path('it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-app');

    expect(lang_path('it/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-special');
});

it('exports strings with --include option', function () {
    initFromStub('exporters.json-groups.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group', '--include' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/special.json')))
        ->assertExitCode(0);

    expect(lang_path('en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-app');

    expect(lang_path('en/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-en-special');

    expect(lang_path('it/app.json'))->not->toBeFile();

    expect(lang_path('it/special.json'))->not->toBeFile();
});

it('exports strings with --exclude option', function () {
    initFromStub('exporters.json-groups.base.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group', '--exclude' => 'en'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/special.json')))
        ->assertExitCode(0);

    expect(lang_path('en/app.json'))->not->toBeFile();

    expect(lang_path('en/special.json'))->not->toBeFile();

    expect(lang_path('it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-app');

    expect(lang_path('it/special.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.base.output-it-special');
});

it('exports strings with warning', function () {
    initFromStub('exporters.json-groups.warnings.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput('Missing key name at line 4. The row will be skipped.')
        ->expectsOutput('app.second at line 5, column 3 (en) is missing. It will be skipped.')
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.json')))
        ->assertExitCode(0);

    expect(lang_path('en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.warnings.output-en-app');

    expect(lang_path('it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.warnings.output-it-app');
});

it('exports strings with no entries', function () {
    $this->artisan(LarexInitCommand::class);

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput('No entries exported.')
        ->assertExitCode(0);
});

it('exports strings trimming whitespaces in group and key', function () {
    initFromStub('exporters.json-groups.spaces.input');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('en/app.json')))
        ->expectsOutput(sprintf("%s created successfully.", lang_rpath('it/app.json')))
        ->assertExitCode(0);

    expect(lang_path('en/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.spaces.output-en-app');

    expect(lang_path('it/app.json'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('exporters.json-groups.spaces.output-it-app');
});
