<?php

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;

it('imports strings', function () {
    File::makeDirectory(lang_path('en'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.json-groups.base.input-en-complex', lang_path('en/complex.json'));
    initFromStub('importers.json-groups.base.input-en-simple', lang_path('en/simple.json'));
    initFromStub('importers.json-groups.base.input-it-complex', lang_path('it/complex.json'));
    initFromStub('importers.json-groups.base.input-it-simple', lang_path('it/simple.json'));

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
    File::makeDirectory(lang_path('en'), 0755, true, true);
    File::makeDirectory(lang_path('fr'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.json-groups.include-exclude.input-en', lang_path('en/app.json'));
    initFromStub('importers.json-groups.include-exclude.input-fr', lang_path('fr/app.json'));
    initFromStub('importers.json-groups.include-exclude.input-it', lang_path('it/app.json'));

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
    File::makeDirectory(lang_path('en'), 0755, true, true);
    File::makeDirectory(lang_path('fr'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.json-groups.include-exclude.input-en', lang_path('en/app.json'));
    initFromStub('importers.json-groups.include-exclude.input-fr', lang_path('fr/app.json'));
    initFromStub('importers.json-groups.include-exclude.input-it', lang_path('it/app.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:group', '--exclude' => 'fr'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-groups.include-exclude.exclude');
});
