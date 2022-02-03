<?php

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;

it('imports strings', function () {
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.json-langs.base.input-en', resource_path('lang/en.json'));
    initFromStub('importers.json-langs.base.input-it', resource_path('lang/it.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:lang'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-langs.base.output');
});

it('imports strings with --include option', function () {
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/fr'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.json-langs.include-exclude.input-en', resource_path('lang/en.json'));
    initFromStub('importers.json-langs.include-exclude.input-fr', resource_path('lang/fr.json'));
    initFromStub('importers.json-langs.include-exclude.input-it', resource_path('lang/it.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:lang', '--include' => 'en,fr'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-langs.include-exclude.include');
});

it('imports strings with --exclude option', function () {
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/fr'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.json-langs.include-exclude.input-en', resource_path('lang/en.json'));
    initFromStub('importers.json-langs.include-exclude.input-fr', resource_path('lang/fr.json'));
    initFromStub('importers.json-langs.include-exclude.input-it', resource_path('lang/it.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:lang', '--exclude' => 'fr'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-langs.include-exclude.exclude');
});
