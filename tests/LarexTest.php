<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;

class LarexTest extends TestCase
{
    public function test_larex_command_without_entries(): void
    {
        $this->artisan('larex:init')->run();
        
        $this->artisan('larex')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('No entries found.')
            ->run();
    }
    
    public function test_larex_command_fail_when_localization_file_not_exists(): void
    {
        $this->artisan('larex')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init')
            ->run();
    }
    
    public function test_larex_command_with_warning(): void
    {
        $this->artisan('larex:init')->run();
        
        File::append(base_path($this->file), $this->getTestStub('warning-input'));
        
        $this->artisan('larex')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('[app|second] on line 3, column 3 is not valid. It will be skipped.')
            ->expectsOutput('resources/lang/en/app.php created successfully.')
            ->run();
        
        self::assertFileExists(resource_path('lang' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'app.php'));
        
        self::assertEquals(
            $this->getTestStub('warning-output'),
            File::get(resource_path('lang' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'app.php'))
        );
    }
    
    public function test_larex_command(): void
    {
        $this->artisan('larex:init')->run();
        
        File::append(base_path($this->file), $this->getTestStub('larex-input'));
        
        $this->artisan('larex')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/en/another.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'app.php'));
        self::assertFileExists(resource_path('lang' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'another.php'));
        
        self::assertEquals(
            $this->getTestStub('larex-output-app'),
            File::get(resource_path('lang' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('larex-output-another'),
            File::get(resource_path('lang' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'another.php'))
        );
    }
}