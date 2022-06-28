<?php

use Lukasss93\Larex\Console\LarexLangAdd;

it('does not add a new language due to invalid language code', function () {
    initFromStub('lang.lang-input');

    $this->artisan(LarexLangAdd::class, ['code' => 'xyz'])
        ->expectsOutput('Invalid language code (xyz)')
        ->assertExitCode(1);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('lang.lang-input');
});

it('does not add a new language due to invalid language code + suggest', function () {
    initFromStub('lang.lang-input');

    $this->artisan(LarexLangAdd::class, ['code' => 'itx'])
        ->expectsOutput('Language code is not valid (itx). Did you mean: it?')
        ->assertExitCode(1);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('lang.lang-input');
});

it('adds a new language column', function () {
    initFromStub('lang.lang-input');

    $this->artisan(LarexLangAdd::class, ['code' => 'es'])
        ->expectsOutput('Added language column: "es"')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('lang.lang-add-output');
});
