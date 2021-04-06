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

        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();

        self::assertEquals(0, $result);
    }

    public function test_failure(): void
    {
        config(['larex.linters' => [
            ValidLanguageCodeLinter::class,
        ]]);

        $this->initFromStub('linters.valid-language-code.failure');

        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  Language code not valid in column 3 (xxx).')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();

        self::assertEquals(1, $result);
    }

    public function test_failure_and_suggest(): void
    {
        config(['larex.linters' => [
            ValidLanguageCodeLinter::class,
        ]]);

        $this->initFromStub('linters.valid-language-code.failure-and-suggest');

        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  Language code not valid in column 3 (it_IF). Did you mean: it_IT')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();

        self::assertEquals(1, $result);
    }
}
