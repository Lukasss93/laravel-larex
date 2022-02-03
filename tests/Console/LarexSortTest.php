<?php

use Lukasss93\Larex\Console\LarexSortCommand;

it('sorts rows', function () {
    initFromStub('sort.sort-input');

    $this->artisan(LarexSortCommand::class)
        ->expectsOutput('Sorting che CSV rows...')
        ->expectsOutput('Sorting completed.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('sort.sort-output');
});

it('does not sort rows due to missing localization file', function () {
    $this->artisan(LarexSortCommand::class)
        ->expectsOutput('Sorting che CSV rows...')
        ->expectsOutput(sprintf("The '%s' does not exists.", csv_path(true)))
        ->expectsOutput('Please create it with: php artisan larex:init')
        ->assertExitCode(1);

    expect(csv_path())->not->toBeFile();
});
