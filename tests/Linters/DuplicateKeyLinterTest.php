<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\DuplicateKeyLinter;

it('passes', function () {
    config([
        'larex.linters' => [
            DuplicateKeyLinter::class,
        ],
    ]);

    initFromStub('linters.duplicate-key.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails', function () {
    config([
        'larex.linters' => [
            DuplicateKeyLinter::class,
        ],
    ]);

    initFromStub('linters.duplicate-key.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  1 duplicate key found:')
        ->expectsOutput('└ 2, 3 (app.apple)')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});
