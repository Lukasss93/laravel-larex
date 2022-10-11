<?php

use Lukasss93\Larex\Console\LarexLangOrderCommand;

test('missing csv', function () {
    $this->artisan(LarexLangOrderCommand::class, ['from' => 'it', 'to' => 'en'])
        ->expectsOutput(sprintf("The '%s' does not exists.", csv_path(true)))
        ->expectsOutput('Please create it with: php artisan larex:init')
        ->assertExitCode(1);
});

test('source language: wrong code', function () {
    initFromStub('console.lang-order.input');

    $this->artisan(LarexLangOrderCommand::class, ['from' => 'de', 'to' => 'en'])
        ->expectsOutput('The source language (de) is not valid.')
        ->assertExitCode(1);
});

test('source language: wrong position', function () {
    initFromStub('console.lang-order.input');

    $this->artisan(LarexLangOrderCommand::class, ['from' => '4', 'to' => 'en'])
        ->expectsOutput('The source language (4) is not valid.')
        ->assertExitCode(1);
});

test('destination language: wrong code', function () {
    initFromStub('console.lang-order.input');

    $this->artisan(LarexLangOrderCommand::class, ['from' => 'it', 'to' => 'de'])
        ->expectsOutput('The destination language (de) is not valid.')
        ->assertExitCode(1);
});

test('destination language: wrong position', function () {
    initFromStub('console.lang-order.input');

    $this->artisan(LarexLangOrderCommand::class, ['from' => 'it', 'to' => '4'])
        ->expectsOutput('The destination language (4) is not valid.')
        ->assertExitCode(1);
});

test('same source and destination language', function ($from, $to) {
    initFromStub('console.lang-order.input');

    $this->artisan(LarexLangOrderCommand::class, ['from' => 'it', 'to' => 'it'])
        ->expectsOutput('The source and destination languages are the same.')
        ->assertExitCode(1);
})->with([
    ['it', 'it'],
    ['it', '2'],
    ['2', 'it'],
    ['1', '1'],
]);

test('order', function ($from, $to) {
    initFromStub('console.lang-order.input');

    $this->artisan(LarexLangOrderCommand::class, ['from' => $from, 'to' => $to])
        ->expectsOutput('Done.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('console.lang-order.output');
})->with([
    'code' => ['it', 'en'],
    'position' => ['2', '1'],
]);
