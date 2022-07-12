<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\SameParametersLinter;

it('passes', function () {
    config([
        'larex.linters' => [SameParametersLinter::class],
    ]);

    initFromStub('linters.same-parameters.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails', function () {
    config([
        'larex.linters' => [SameParametersLinter::class],
    ]);

    initFromStub('linters.same-parameters.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  Missing parameters found:')
        ->expectsOutput('├ line 2 (app.hello), column 4 (it): missing :age parameter')
        ->expectsOutput('├ line 2 (app.hello), column 5 (es): missing :name parameter')
        ->expectsOutput('├ line 3 (app.cat), column 3 (en): missing :thing parameter')
        ->expectsOutput('├ line 3 (app.cat), column 5 (es): missing :animal parameter')
        ->expectsOutput('├ line 3 (app.cat), column 5 (es): missing :thing parameter')
        ->expectsOutput('└ line 6 (app.bye), column 5 (es): missing :name parameter')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});

it('fails ignoring empty values', function () {
    config([
        'larex.linters' => [SameParametersLinter::class],
        'larex.ignore_empty_values' => true,
    ]);

    initFromStub('linters.same-parameters.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  Missing parameters found:')
        ->expectsOutput('├ line 2 (app.hello), column 4 (it): missing :age parameter')
        ->expectsOutput('├ line 2 (app.hello), column 5 (es): missing :name parameter')
        ->expectsOutput('├ line 3 (app.cat), column 3 (en): missing :thing parameter')
        ->expectsOutput('├ line 3 (app.cat), column 5 (es): missing :animal parameter')
        ->expectsOutput('└ line 3 (app.cat), column 5 (es): missing :thing parameter')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});
