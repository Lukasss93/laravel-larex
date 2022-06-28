<?php

use Lukasss93\Larex\Console\LarexLangRemove;

it('fails due to missing language column', function () {
    initFromStub('lang.lang-input');

    $this->artisan(LarexLangRemove::class, ['code' => 'es'])
        ->expectsOutput('The language code "es" is not present in the CSV file.')
        ->assertExitCode(1);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('lang.lang-input');
});

it('remove a language column', function () {
    initFromStub('lang.lang-input');

    $this->artisan(LarexLangRemove::class, ['code' => 'en'])
        ->expectsOutput('The language code "en" has been removed from the CSV file.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('lang.lang-remove-output');
});
