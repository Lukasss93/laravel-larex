<?php

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\UntranslatedStringsLinter;

it('passes', function () {
    config([
        'larex.linters' => [
            UntranslatedStringsLinter::class,
        ],
    ]);

    initFromStub('linters.untranslated-strings.blade', resource_path('views/test.blade.php'));
    initFromStub('linters.untranslated-strings.success');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput('OK (1 linter)')
        ->assertExitCode(0);
});

it('fails', function () {
    config([
        'larex.linters' => [
            UntranslatedStringsLinter::class,
        ],
    ]);

    $testFilePath = initFromStub('linters.untranslated-strings.blade', resource_path('views/test.blade.php'));
    initFromStub('linters.untranslated-strings.failure');

    $this->artisan(LarexLintCommand::class)
        ->expectsOutput(' FAIL  1 untranslated string found:')
        ->expectsOutput('└ app.news is untranslated at line 90, column 63 in '.$testFilePath)
        ->expectsOutput('FAILURES!')
        ->expectsOutput('Linters: 1, Failures: 1')
        ->assertExitCode(1);
});
