<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\ValidHtmlValueLinter;

it('passes', function () {
    config([
        'larex.linters' => [
            ValidHtmlValueLinter::class,
        ],
    ]);

    initFromStub('linters.valid-html-value.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails', function () {
    config([
        'larex.linters' => [
            ValidHtmlValueLinter::class,
        ],
    ]);

    initFromStub('linters.valid-html-value.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  2 invalid html strings found:')
        ->expectsOutput('├ line 2 (app.apple), column: 3 (en)')
        ->expectsOutput('└ line 3 (app.ark), column: 4 (it)')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});
