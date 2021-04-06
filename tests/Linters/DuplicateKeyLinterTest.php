<?php

namespace Lukasss93\Larex\Tests\Linters;

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\DuplicateKeyLinter;
use Lukasss93\Larex\Tests\TestCase;

class DuplicateKeyLinterTest extends TestCase
{
    public function test_successful(): void
    {
        config(['larex.linters' => [
            DuplicateKeyLinter::class,
        ]]);

        $this->initFromStub('linters.duplicate-key.success');

        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();

        self::assertEquals(0, $result);
    }

    public function test_failure(): void
    {
        config(['larex.linters' => [
            DuplicateKeyLinter::class,
        ]]);

        $this->initFromStub('linters.duplicate-key.failure');

        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  1 duplicate key found:')
            ->expectsOutput('└ 2, 3 (app.apple)')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();

        self::assertEquals(1, $result);
    }
}
