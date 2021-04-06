<?php

namespace Lukasss93\Larex\Tests\Linters;

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\ConcurrentKeyLinter;
use Lukasss93\Larex\Tests\TestCase;

class ConcurrentKeyLinterTest extends TestCase
{
    public function test_successful(): void
    {
        config(['larex.linters' => [
            ConcurrentKeyLinter::class,
        ]]);

        $this->initFromStub('linters.concurrent-key.success');

        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();

        self::assertEquals(0, $result);
    }

    public function test_failure(): void
    {
        config(['larex.linters' => [
            ConcurrentKeyLinter::class,
        ]]);

        $this->initFromStub('linters.concurrent-key.failure');

        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  Concurrent keys found:')
            ->expectsOutput('├ rows 2 (add.a.b), 3 (add.a.b.c), 4 (add.a);')
            ->expectsOutput('├ rows 8 (add.d), 9 (add.d.a);')
            ->expectsOutput('├ rows 10 (app.e.a), 11 (app.e);')
            ->expectsOutput('├ rows 12 (app.f), 13 (app.f.a), 14 (app.f.b);')
            ->expectsOutput('└ rows 15 (app.g.a), 17 (app.g.a.b);')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();

        self::assertEquals(1, $result);
    }
}
