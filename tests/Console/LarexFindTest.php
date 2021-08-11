<?php

use Lukasss93\Larex\Console\LarexFindCommand;

it('does not find rows due to missing localization file', function () {
    $this
        ->artisan(LarexFindCommand::class, ['value' => 'test'])
        ->expectsOutput(sprintf("The '%s' does not exists.", localizationPath(true)))
        ->expectsOutput('Please create it with: php artisan larex:init or php artisan larex:import')
        ->assertExitCode(1);
});

it('finds no strings', function () {
    initFromStub('find.input');

    $this->artisan(LarexFindCommand::class, ['value' => 'test'])
        ->expectsOutput('No string found.')
        ->assertExitCode(0);
});

it('finds strings', function () {
    initFromStub('find.input');

    $this->artisan(LarexFindCommand::class, ['value' => 'app'])
        ->expectsTable(['group', 'key', 'en'], [
            ['app', 'car', 'Car'],
            ['app', 'apple', 'Apple'],
        ])
        ->assertExitCode(0);
});
