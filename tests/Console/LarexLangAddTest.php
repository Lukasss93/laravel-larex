<?php

use Lukasss93\Larex\Console\LarexLangAddCommand;

it('does not add a new language due to invalid language code', function () {
    initFromStub('console.lang-add.lang-input');

    $this->artisan(LarexLangAddCommand::class, ['code' => 'xyz'])
        ->expectsOutput('Invalid language code (xyz)')
        ->assertExitCode(1);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('console.lang-add.lang-input');
});

it('does not add a new language due to invalid language code + suggest', function () {
    initFromStub('console.lang-add.lang-input');

    $this->artisan(LarexLangAddCommand::class, ['code' => 'itx'])
        ->expectsOutput('Language code is not valid (itx). Did you mean: it?')
        ->assertExitCode(1);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('console.lang-add.lang-input');
});

it('adds a new language column', function () {
    initFromStub('console.lang-add.lang-input');

    $this->artisan(LarexLangAddCommand::class, ['code' => 'es'])
        ->expectsOutput('Added language column: "es"')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('console.lang-add.lang-add-output');
});
