<?php

namespace Lukasss93\Larex\Tests\Linters;

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\ValidLanguageCodeLinter;
use Lukasss93\Larex\Tests\TestCase;

class ValidLanguageCodeLinterTest extends TestCase
{
    public function test_successful(): void
    {
        config(['larex.linters' => [
            ValidLanguageCodeLinter::class,
        ]]);

        $this->initFromStub('linters.valid-language-code.success');

        $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->assertExitCode(0);
    }

    public function test_failure(): void
    {
        config(['larex.linters' => [
            ValidLanguageCodeLinter::class,
        ]]);

        $this->initFromStub('linters.valid-language-code.failure');

        $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  Language code not valid in column 3 (xxx).')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->assertExitCode(1);
    }

    public function test_failure_and_suggest(): void
    {
        config(['larex.linters' => [
            ValidLanguageCodeLinter::class,
        ]]);

        $this->initFromStub('linters.valid-language-code.failure-and-suggest');

        $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  Language code not valid in column 3 (it_IF). Did you mean: it_IT')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->assertExitCode(1);
    }
}
