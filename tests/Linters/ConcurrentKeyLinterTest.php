<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\ConcurrentKeyLinter;

it('passes', function () {
    config([
        'larex.linters' => [
            ConcurrentKeyLinter::class,
        ],
    ]);

    initFromStub('linters.concurrent-key.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails', function () {
    config([
        'larex.linters' => [
            ConcurrentKeyLinter::class,
        ],
    ]);

    initFromStub('linters.concurrent-key.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  Concurrent keys found:')
        ->expectsOutput('├ rows 2 (add.a.b), 3 (add.a.b.c), 4 (add.a);')
        ->expectsOutput('├ rows 8 (add.d), 9 (add.d.a);')
        ->expectsOutput('├ rows 10 (app.e.a), 11 (app.e);')
        ->expectsOutput('├ rows 12 (app.f), 13 (app.f.a), 14 (app.f.b);')
        ->expectsOutput('└ rows 15 (app.g.a), 17 (app.g.a.b);')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});
