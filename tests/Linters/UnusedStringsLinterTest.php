<?php

namespace Lukasss93\Larex\Tests\Linters;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\UnusedStringsLinter;
use Lukasss93\Larex\Tests\TestCase;

class UnusedStringsLinterTest extends TestCase
{
    public function test_successful(): void
    {
        config(['larex.linters' => [
            UnusedStringsLinter::class,
        ]]);
        
        $this->initFromStub('linters/unused-strings/success');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();
        
        self::assertEquals(0, $result);
    }
    
    public function test_failure(): void
    {
        config(['larex.linters' => [
            UnusedStringsLinter::class,
        ]]);
        
        $this->initFromStub('linters/unused-strings/failure');
        
        $result = $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  1 unused string found:')
            ->expectsOutput('└ app.apple is unused at line 4')
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
        
        self::assertEquals(1, $result);
    }
    
    /**
     * @beforeClass
     */
    public function beforeAll(): void
    {
        //create a test blade file with @lang functions
        $testFilePath = resource_path('views' . PHP_EOL . 'test.blade.php');
        
        if (File::exists($testFilePath)) {
            File::delete($testFilePath);
        }
        
        File::put($testFilePath, $this->getTestStub('linters/unused-strings/blade'));
    }
}