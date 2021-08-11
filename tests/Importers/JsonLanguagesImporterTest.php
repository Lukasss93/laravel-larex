<?php

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;

it('imports strings', function () {
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.json-langs.input-en', resource_path('lang/en.json'));
    initFromStub('importers.json-langs.input-it', resource_path('lang/it.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:lang'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(localizationPath())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-langs.output');
});

