<?php

namespace Lukasss93\Larex\Tests\Linters;

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\NoValueLinter;
use Lukasss93\Larex\Tests\TestCase;

class NoValueLinterTest extends TestCase
{
    public function test_successful(): void
    {
        config(['larex.linters' => [
            NoValueLinter::class,
        ]]);
        
        $this->initFromStub('linters/no-value/success');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();
        
        self::assertEquals(0, $result);
    }
    
    public function test_failure(): void
    {
        config(['larex.linters' => [
            NoValueLinter::class,
        ]]);
        
        $this->initFromStub('linters/no-value/failure');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  4 missing values found:')
            ->expectsOutput('├ row 2 (app.a), column 3 (en)')
            ->expectsOutput('├ row 3 (app.b), column 4 (it)')
            ->expectsOutput('├ row 4 (app.c), column 3 (en)')
            ->expectsOutput('└ row 4 (app.c), column 4 (it)')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
        
        self::assertEquals(1, $result);
    }
}