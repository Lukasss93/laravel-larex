<?php

namespace Lukasss93\Larex\Tests\Linters;

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\ValidHtmlValueLinter;
use Lukasss93\Larex\Tests\TestCase;

class ValidHtmlValueLinterTest extends TestCase
{
    public function test_successful(): void
    {
        config(['larex.linters' => [
            ValidHtmlValueLinter::class,
        ]]);
        
        $this->initFromStub('linters/valid-html-value/success');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();
        
        self::assertEquals(0, $result);
    }
    
    public function test_failure(): void
    {
        config(['larex.linters' => [
            ValidHtmlValueLinter::class,
        ]]);
        
        $this->initFromStub('linters/valid-html-value/failure');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  2 invalid html strings found:')
            ->expectsOutput('├ line 2 (app.apple), column: 3 (en)')
            ->expectsOutput('└ line 3 (app.ark), column: 4 (it)')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
        
        self::assertEquals(1, $result);
    }
}