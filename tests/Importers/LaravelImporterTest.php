<?php

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;

it('imports strings', function () {
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.laravel.base.input-en-complex', resource_path('lang/en/complex.php'));
    initFromStub('importers.laravel.base.input-en-simple', resource_path('lang/en/simple.php'));
    initFromStub('importers.laravel.base.input-it-complex', resource_path('lang/it/complex.php'));
    initFromStub('importers.laravel.base.input-it-simple', resource_path('lang/it/simple.php'));

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
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/fr'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.laravel.include-exclude.input-en', resource_path('lang/en/app.php'));
    initFromStub('importers.laravel.include-exclude.input-fr', resource_path('lang/fr/app.php'));
    initFromStub('importers.laravel.include-exclude.input-it', resource_path('lang/it/app.php'));

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
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/fr'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.laravel.include-exclude.input-en', resource_path('lang/en/app.php'));
    initFromStub('importers.laravel.include-exclude.input-fr', resource_path('lang/fr/app.php'));
    initFromStub('importers.laravel.include-exclude.input-it', resource_path('lang/it/app.php'));

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
    File::makeDirectory(resource_path('lang/en_GB'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.laravel.territory.input-en_GB-complex', resource_path('lang/en_GB/complex.php'));
    initFromStub('importers.laravel.territory.input-en_GB-simple', resource_path('lang/en_GB/simple.php'));
    initFromStub('importers.laravel.territory.input-it-complex', resource_path('lang/it/complex.php'));
    initFromStub('importers.laravel.territory.input-it-simple', resource_path('lang/it/simple.php'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'laravel'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.laravel.territory.output');
});
