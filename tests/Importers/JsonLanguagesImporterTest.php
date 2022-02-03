<?php

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;

it('imports strings', function () {
    File::makeDirectory(lang_path('en'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.json-langs.base.input-en', lang_path('en.json'));
    initFromStub('importers.json-langs.base.input-it', lang_path('it.json'));

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
    File::makeDirectory(lang_path('en'), 0755, true, true);
    File::makeDirectory(lang_path('fr'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.json-langs.include-exclude.input-en', lang_path('en.json'));
    initFromStub('importers.json-langs.include-exclude.input-fr', lang_path('fr.json'));
    initFromStub('importers.json-langs.include-exclude.input-it', lang_path('it.json'));

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
    File::makeDirectory(lang_path('en'), 0755, true, true);
    File::makeDirectory(lang_path('fr'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.json-langs.include-exclude.input-en', lang_path('en.json'));
    initFromStub('importers.json-langs.include-exclude.input-fr', lang_path('fr.json'));
    initFromStub('importers.json-langs.include-exclude.input-it', lang_path('it.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:lang', '--exclude' => 'fr'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-langs.include-exclude.exclude');
});
