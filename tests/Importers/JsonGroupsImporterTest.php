<?php

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;

it('imports strings', function () {
    File::makeDirectory(resource_path('lang/en'), 0755, true, true);
    File::makeDirectory(resource_path('lang/it'), 0755, true, true);

    initFromStub('importers.json-groups.input-en-complex', resource_path('lang/en/complex.json'));
    initFromStub('importers.json-groups.input-en-simple', resource_path('lang/en/simple.json'));
    initFromStub('importers.json-groups.input-it-complex', resource_path('lang/it/complex.json'));
    initFromStub('importers.json-groups.input-it-simple', resource_path('lang/it/simple.json'));

    $this->artisan(LarexImportCommand::class, ['importer' => 'json:group'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(localizationPath())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importers.json-groups.output');
});
