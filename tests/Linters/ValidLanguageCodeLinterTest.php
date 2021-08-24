<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\ValidLanguageCodeLinter;

it('passes', function () {
    config([
        'larex.linters' => [
            ValidLanguageCodeLinter::class,
        ],
    ]);

    initFromStub('linters.valid-language-code.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails', function () {
    config([
        'larex.linters' => [
            ValidLanguageCodeLinter::class,
        ],
    ]);

    initFromStub('linters.valid-language-code.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  Language code not valid in column 3 (xxx).')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});

it('fails and suggest', function () {
    config([
        'larex.linters' => [
            ValidLanguageCodeLinter::class,
        ],
    ]);

    initFromStub('linters.valid-language-code.failure-and-suggest');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  Language code not valid in column 3 (it-IF). Did you mean: it-IT')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});
