<?php

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;

it('imports strings | base', function () {
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

    expect(localizationPath())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.laravel.base.output');
});

it('imports strings | territory', function () {
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

    expect(localizationPath())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.laravel.territory.output');
});
