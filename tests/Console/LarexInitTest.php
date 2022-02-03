<?php

use Lukasss93\Larex\Console\LarexInitCommand;
use Lukasss93\Larex\Support\Utils;

it('initializes localization file', function () {
    $this->artisan(LarexInitCommand::class)
        ->expectsOutput(sprintf('%s created successfully.', csv_path(true)))
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqual(Utils::getStub('default'));
});

it('initializes localization file with --base option', function () {
    $this->artisan(LarexInitCommand::class, ['--base' => true])
        ->expectsOutput(sprintf('%s created successfully.', csv_path(true)))
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqual(Utils::getStub('base'));
});

it('does not initialize localization file due to file already exists', function () {
    $this->artisan(LarexInitCommand::class)->run();

    $this->artisan(LarexInitCommand::class)
        ->expectsOutput(sprintf('%s already exists.', csv_path(true)))
        ->assertExitCode(1);

    expect(csv_path())
        ->toBeFile();
});
