<?php

namespace Lukasss93\Larex\Tests\Linters;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Linters\UntranslatedStringsLinter;
use Lukasss93\Larex\Tests\TestCase;

class UntranslatedStringsLinterTest extends TestCase
{
    public function test_successful(): void
    {
        config(['larex.linters' => [
            UntranslatedStringsLinter::class,
        ]]);
        
        $this->initFromStub('linters/untranslated-strings/success');
        
        $this->artisan(LarexLintCommand::class)
            ->expectsOutput('OK (1 linter)')
            ->run();
    }
    
    public function test_failure(): void
    {
        config(['larex.linters' => [
            UntranslatedStringsLinter::class,
        ]]);
        
        $testFilePath = resource_path('views' . DIRECTORY_SEPARATOR . 'test.blade.php');
        
        $this->initFromStub('linters/untranslated-strings/failure');
        
        $this->artisan(LarexLintCommand::class)
            ->expectsOutput(' FAIL  1 untranslated string found:')
            ->expectsOutput('└ app.news is untranslated at line 90, column 63 in ' . $testFilePath)
            ->expectsOutput('FAILURES!')
            ->expectsOutput('Linters: 1, Failures: 1')
            ->run();
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
        
        File::put($testFilePath, $this->getTestStub('linters/untranslated-strings/blade'));
    }
}