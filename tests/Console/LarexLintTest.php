<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\DuplicateKeyLinter;
use Lukasss93\Larex\Linters\ValidHeaderLinter;

it('does not lint due to missing localization file', function () {
    config(['larex.linters' => []]);

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(sprintf("The '%s' does not exists.", localizationPath(true)))
        ->expectsOutput('Please create it with: php artisan larex:init')
        ->assertExitCode(1);
});

it('does not lint due to missing linters', function () {
    config(['larex.linters' => []]);
    initFromStub('lint.no-linters');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('No linters executed!')
        ->assertExitCode(1);
});

it('lints with failure', function () {
    config([
        'larex.linters' => [
            DuplicateKeyLinter::class,
        ],
    ]);
    initFromStub('lint.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  1 duplicate key found:')
        ->expectsOutput('└ 2, 3 (app.a)')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});

it('lints successfully', function () {
    config([
        'larex.linters' => [
            ValidHeaderLinter::class,
        ],
    ]);
    initFromStub('lint.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});
