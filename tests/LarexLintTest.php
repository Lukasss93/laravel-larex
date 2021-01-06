<?php

namespace Lukasss93\Larex\Tests;

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\DuplicateKeyLinter;
use Lukasss93\Larex\Linters\ValidHeaderLinter;

class LarexLintTest extends TestCase
{
    public function test_lint_command_no_linters(): void
    {
        config(['larex.linters'=>[]]);
        
        $this->artisan(LarexLintCommand::class)
            ->expectsOutput('No linters executed!')
            ->run();
    }
    
    public function test_lint_command_failure(): void
    {
        config(['larex.linters'=>[
            DuplicateKeyLinter::class,
        ]]);
        
        $this->initFromStub('lint/base-failure');
        
        $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  1 duplicate key found:')
            ->expectsOutput('└ 2, 3 (app.a)')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
    }
    
    public function test_lint_command_ok(): void
    {
        config(['larex.linters'=>[
            ValidHeaderLinter::class,
        ]]);
        
        $this->initFromStub('lint/base-ok');
        
        $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();
    }
}