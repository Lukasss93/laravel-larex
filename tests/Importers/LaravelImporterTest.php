<?php

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;

it('imports strings', function () {
    File::makeDirectory(lang_path('en'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.laravel.base.input-en-complex', lang_path('en/complex.php'));
    initFromStub('importers.laravel.base.input-en-simple', lang_path('en/simple.php'));
    initFromStub('importers.laravel.base.input-it-complex', lang_path('it/complex.php'));
    initFromStub('importers.laravel.base.input-it-simple', lang_path('it/simple.php'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'laravel'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.laravel.base.output');
});

it('imports strings with --include option', function () {
    File::makeDirectory(lang_path('en'), 0755, true, true);
    File::makeDirectory(lang_path('fr'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.laravel.include-exclude.input-en', lang_path('en/app.php'));
    initFromStub('importers.laravel.include-exclude.input-fr', lang_path('fr/app.php'));
    initFromStub('importers.laravel.include-exclude.input-it', lang_path('it/app.php'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'laravel', '--include' => 'en,fr'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.laravel.include-exclude.include');
});

it('imports strings with --exclude option', function () {
    File::makeDirectory(lang_path('en'), 0755, true, true);
    File::makeDirectory(lang_path('fr'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.laravel.include-exclude.input-en', lang_path('en/app.php'));
    initFromStub('importers.laravel.include-exclude.input-fr', lang_path('fr/app.php'));
    initFromStub('importers.laravel.include-exclude.input-it', lang_path('it/app.php'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'laravel', '--exclude' => 'fr'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.laravel.include-exclude.exclude');
});

it('imports strings with territory', function () {
    File::makeDirectory(lang_path('en_GB'), 0755, true, true);
    File::makeDirectory(lang_path('it'), 0755, true, true);

    initFromStub('importers.laravel.territory.input-en_GB-complex', lang_path('en_GB/complex.php'));
    initFromStub('importers.laravel.territory.input-en_GB-simple', lang_path('en_GB/simple.php'));
    initFromStub('importers.laravel.territory.input-it-complex', lang_path('it/complex.php'));
    initFromStub('importers.laravel.territory.input-it-simple', lang_path('it/simple.php'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'laravel'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.laravel.territory.output');
});
