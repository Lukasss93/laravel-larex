<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\UnusedStringsLinter;

it('passes', function () {
    config([
        'larex.linters' => [
            UnusedStringsLinter::class,
        ],
    ]);

    initFromStub('linters.untranslated-strings.blade', resource_path('views/test.blade.php'));
    initFromStub('linters.unused-strings.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails', function () {
    config([
        'larex.linters' => [
            UnusedStringsLinter::class,
        ],
    ]);

    initFromStub('linters.untranslated-strings.blade', resource_path('views/test.blade.php'));
    initFromStub('linters.unused-strings.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  1 unused string found:')
        ->expectsOutput('└ app.apple is unused at line 4')
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});
