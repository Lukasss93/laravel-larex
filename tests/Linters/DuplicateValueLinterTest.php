<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\DuplicateValueLinter;

it('passes', function () {
    config([
        'larex.linters' => [
            DuplicateValueLinter::class,
        ],
    ]);

    initFromStub('linters.duplicate-value.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails', function () {
    config([
        'larex.linters' => [
            DuplicateValueLinter::class,
        ],
    ]);

    initFromStub('linters.duplicate-value.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  1 duplicate value found:')
        ->expectsOutput('└ row 3 (app.ark), columns: 3 (en), 4 (it)')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});

