<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;

class LarexExportTest extends TestCase
{
    public function test_larex_command_without_entries(): void
    {
        $this->artisan('larex:init')->run();
        
        $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('No entries found.')
            ->run();
    }
    
    public function test_larex_command_fail_when_localization_file_not_exists(): void
    {
        $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init')
            ->run();
    }
    
    public function test_larex_command_fail_when_include_exclude_are_together(): void
    {
        $this->initFromStub('export/larex-input');
        
        $this->artisan('larex:export --include= --exclude=')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("The --include and --exclude options can be used only one at a time.")
            ->run();
    }
    
    /** @dataProvider providerWarning
     * @param string $stub
     * @param string $output
     */
    public function test_larex_command_with_warning(string $stub, string $output): void
    {
        $this->initFromStub($stub);
        
        $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput($output)
            ->expectsOutput('resources/lang/en/app.php created successfully.')
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/warning-output'),
            File::get(resource_path('lang/en/app.php'))
        );
    }
    
    public function test_larex_command(): void
    {
        $this->initFromStub('export/larex-input');
        
        $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/en/another.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->expectsOutput("resources/lang/it/another.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/en/another.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        self::assertFileExists(resource_path('lang/it/another.php'));
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-app'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-another'),
            File::get(resource_path('lang/en/another.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-app'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-another'),
            File::get(resource_path('lang/it/another.php'))
        );
    }
    
    public function test_larex_watch(): void
    {
        $this->initFromStub('export/larex-input');
        
        $this->artisan('larex:export --watch')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/en/another.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->expectsOutput("resources/lang/it/another.php created successfully.")
            ->expectsOutput('Waiting for changes...')
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/en/another.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        self::assertFileExists(resource_path('lang/it/another.php'));
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-app'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-another'),
            File::get(resource_path('lang/en/another.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-app'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-another'),
            File::get(resource_path('lang/it/another.php'))
        );
    }
    
    public function test_larex_command_with_numeric_keys(): void
    {
        $this->initFromStub('export/numeric/input');
        
        $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/numeric/output-en'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/numeric/output-it'),
            File::get(resource_path('lang/it/app.php'))
        );
    }
    
    public function test_larex_command_with_empty_values(): void
    {
        $this->initFromStub('export/empty/input');
        
        $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/empty/output-en'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/empty/output-it'),
            File::get(resource_path('lang/it/app.php'))
        );
    }
    
    public function test_larex_command_with_enclosures(): void
    {
        $this->initFromStub('export/enclosure/input');
        
        $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        
        self::assertEquals(
            $this->getTestStub('export/enclosure/output-en'),
            File::get(resource_path('lang/en/app.php'))
        );
    }
    
    public function test_larex_with_include_empty(): void
    {
        $this->initFromStub('export/larex-input');
        
        $this->artisan('larex:export --include=')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('No entries found.')
            ->run();
        
    }
    
    public function test_larex_with_include(): void
    {
        $this->initFromStub('export/larex-input');
        
        $this->artisan('larex:export --include=it')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->expectsOutput("resources/lang/it/another.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/it/app.php'));
        self::assertFileExists(resource_path('lang/it/another.php'));
        self::assertFalse(File::exists(resource_path('lang/en/app.php')));
        self::assertFalse(File::exists(resource_path('lang/en/another.php')));
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-app'),
            File::get(resource_path('lang/it/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-another'),
            File::get(resource_path('lang/it/another.php'))
        );
    }
    
    public function test_larex_with_exclude_empty(): void
    {
        $this->initFromStub('export/larex-input');
    
        $this->artisan('larex:export --exclude=')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/en/another.php created successfully.")
            ->expectsOutput("resources/lang/it/app.php created successfully.")
            ->expectsOutput("resources/lang/it/another.php created successfully.")
            ->run();
    
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/en/another.php'));
        self::assertFileExists(resource_path('lang/it/app.php'));
        self::assertFileExists(resource_path('lang/it/another.php'));
    
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-app'),
            File::get(resource_path('lang/en/app.php'))
        );
    
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-another'),
            File::get(resource_path('lang/en/another.php'))
        );
    
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-app'),
            File::get(resource_path('lang/it/app.php'))
        );
    
        self::assertEquals(
            $this->getTestStub('export/larex-output-it-another'),
            File::get(resource_path('lang/it/another.php'))
        );
        
    }
    
    public function test_larex_with_exclude(): void
    {
        $this->initFromStub('export/larex-input');
        
        $this->artisan('larex:export --exclude=it')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en/app.php created successfully.")
            ->expectsOutput("resources/lang/en/another.php created successfully.")
            ->run();
        
        self::assertFileExists(resource_path('lang/en/app.php'));
        self::assertFileExists(resource_path('lang/en/another.php'));
        self::assertFalse(File::exists(resource_path('lang/it/app.php')));
        self::assertFalse(File::exists(resource_path('lang/it/another.php')));
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-app'),
            File::get(resource_path('lang/en/app.php'))
        );
        
        self::assertEquals(
            $this->getTestStub('export/larex-output-en-another'),
            File::get(resource_path('lang/en/another.php'))
        );
    }
    
    public function providerWarning(): array
    {
        return [
            'blank line' => ['export/warning-input-1', 'Line 3 is not valid. It will be skipped.'],
            'missing key' => ['export/warning-input-2', 'Line 3 is not valid. It will be skipped.'],
            'missing column' => [
                'export/warning-input-3',
                '[app|second] on line 3, column 3 (en) is not valid. It will be skipped.'
            ],
        ];
    }
}