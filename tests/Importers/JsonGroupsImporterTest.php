<?php

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;

it('imports strings', function () {
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.json-groups.base.input-en-complex', resource_path('lang/en/complex.json'));
    initFromStub('importers.json-groups.base.input-en-simple', resource_path('lang/en/simple.json'));
    initFromStub('importers.json-groups.base.input-it-complex', resource_path('lang/it/complex.json'));
    initFromStub('importers.json-groups.base.input-it-simple', resource_path('lang/it/simple.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:group'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-groups.base.output');
});

it('imports strings with --include option', function () {
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/fr'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.json-groups.include-exclude.input-en', resource_path('lang/en/app.json'));
    initFromStub('importers.json-groups.include-exclude.input-fr', resource_path('lang/fr/app.json'));
    initFromStub('importers.json-groups.include-exclude.input-it', resource_path('lang/it/app.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:group', '--include' => 'en,fr'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-groups.include-exclude.include');
});

it('imports strings with --exclude option', function () {
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/fr'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.json-groups.include-exclude.input-en', resource_path('lang/en/app.json'));
    initFromStub('importers.json-groups.include-exclude.input-fr', resource_path('lang/fr/app.json'));
    initFromStub('importers.json-groups.include-exclude.input-it', resource_path('lang/it/app.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:group', '--exclude' => 'fr'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-groups.include-exclude.exclude');
});
