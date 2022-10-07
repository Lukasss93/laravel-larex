<?php

use Lukasss93\Larex\Console\LarexLangRemoveCommand;

it('fails due to missing language column', function () {
    initFromStub('console.lang-remove.lang-input');

    $this->artisan(LarexLangRemoveCommand::class, ['code' => 'es'])
        ->expectsOutput('The language code "es" is not present in the CSV file.')
        ->assertExitCode(1);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('console.lang-remove.lang-input');
});

it('remove a language column', function () {
    initFromStub('console.lang-remove.lang-input');

    $this->artisan(LarexLangRemoveCommand::class, ['code' => 'en'])
        ->expectsOutput('The language code "en" has been removed from the CSV file.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('console.lang-remove.lang-remove-output');
});
