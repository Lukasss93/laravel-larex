<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\ValidHeaderLinter;

it('passes', function () {
    config([
        'larex.linters' => [
            ValidHeaderLinter::class,
        ],
    ]);

    initFromStub('linters.valid-header.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails due to missing first column', function () {
    config([
        'larex.linters' => [
            ValidHeaderLinter::class,
        ],
    ]);

    initFromStub('linters.valid-header.missing-first-column');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  First header column is missing.')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});

it('fails due to invalid first column', function () {
    config([
        'larex.linters' => [
            ValidHeaderLinter::class,
        ],
    ]);

    initFromStub('linters.valid-header.invalid-first-column');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  First header column value is invalid. Must be "group".')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});

it('fails due to missing second column', function () {
    config([
        'larex.linters' => [
            ValidHeaderLinter::class,
        ],
    ]);

    initFromStub('linters.valid-header.missing-second-column');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  Second header column is missing.')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});

it('fails due to invalid second column', function () {
    config([
        'larex.linters' => [
            ValidHeaderLinter::class,
        ],
    ]);

    initFromStub('linters.valid-header.invalid-second-column');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  Second header column value is invalid. Must be "key".')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});

it('fails due to no language columns', function () {
    config([
        'larex.linters' => [
            ValidHeaderLinter::class,
        ],
    ]);

    initFromStub('linters.valid-header.no-language-columns');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  No language columns found.')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});
