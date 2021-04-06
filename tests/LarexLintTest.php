<?php

namespace Lukasss93\Larex\Tests;

use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\DuplicateKeyLinter;
use Lukasss93\Larex\Linters\ValidHeaderLinter;

class LarexLintTest extends TestCase
{
    public function test_lint_command_no_csv(): void
    {
        config(['larex.linters' => []]);

        $this->artisan(LarexLintCommand::class)
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init')
            ->assertExitCode(1);
    }

    public function test_lint_command_no_linters(): void
    {
        config(['larex.linters' => []]);

        $this->initFromStub('lint.no-linters');

        $this->artisan(LarexLintCommand::class)
            ->expectsOutput('No linters executed!')
            ->assertExitCode(-1);
    }

    public function test_lint_command_failure(): void
    {
        config(['larex.linters' => [
            DuplicateKeyLinter::class,
        ]]);

        $this->initFromStub('lint.failure');

        $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  1 duplicate key found:')
            ->expectsOutput('└ 2, 3 (app.a)')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->assertExitCode(1);
    }

    public function test_lint_command_success(): void
    {
        config(['larex.linters' => [
            ValidHeaderLinter::class,
        ]]);

        $this->initFromStub('lint.success');

        $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->assertExitCode(0);
    }
}
