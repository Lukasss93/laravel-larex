<?php

namespace Lukasss93\Larex\Tests\Linters;

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\DuplicateValueLinter;
use Lukasss93\Larex\Tests\TestCase;

class DuplicateValueLinterTest extends TestCase
{
    public function test_successful(): void
    {
        config(['larex.linters' => [
            DuplicateValueLinter::class,
        ]]);
        
        $this->initFromStub('linters/duplicate-value/success');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();
        
        self::assertEquals(0, $result);
    }
    
    public function test_failure(): void
    {
        config(['larex.linters' => [
            DuplicateValueLinter::class,
        ]]);
        
        $this->initFromStub('linters/duplicate-value/failure');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  1 duplicate value found:')
            ->expectsOutput('└ row 3 (app.ark), columns: 3 (en), 4 (it)')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
        
        self::assertEquals(1, $result);
    }
}