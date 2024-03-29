<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\NoValueLinter;

it('passes', function () {
    config([
        'larex.linters' => [
            NoValueLinter::class,
        ],
    ]);

    initFromStub('linters.no-value.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails', function () {
    config([
        'larex.linters' => [
            NoValueLinter::class,
        ],
    ]);

    initFromStub('linters.no-value.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  4 missing values found:')
        ->expectsOutput('├ row 2 (app.a), column 3 (en)')
        ->expectsOutput('├ row 3 (app.b), column 4 (it)')
        ->expectsOutput('├ row 4 (app.c), column 3 (en)')
        ->expectsOutput('└ row 4 (app.c), column 4 (it)')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});
