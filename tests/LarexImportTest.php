<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;

class LarexImportTest extends TestCase
{
    /**
     * @param string $firstFile
     * @param string $secondFile
     * @param string $outputFile
     * @dataProvider providerImportCommand
     */
    public function test_larex_import_command(string $firstFile, string $secondFile, string $outputFile): void
    {
        File::makeDirectory(resource_path('lang/en'), 0755, true, true);
        File::makeDirectory(resource_path('lang/it'), 0755, true, true);
        File::put(resource_path('lang/en/app.php'), $this->getTestStub($firstFile));
        File::put(resource_path('lang/it/app.php'), $this->getTestStub($secondFile));
        
        $result = $this->artisan('larex:import')
            ->expectsOutput('Importing entries...')
            ->expectsOutput('Files imported successfully.')
            ->run();
        
        self::assertFileExists(base_path($this->file));
        self::assertEquals($this->getTestStub($outputFile), File::get(base_path($this->file)));
        self::assertEquals(0, $result);
    }
    
    public function test_larex_import_command_with_no_force(): void
    {
        $this->artisan('larex:init')->run();
        
        File::makeDirectory(resource_path('lang/en'), 0755, true, true);
        File::makeDirectory(resource_path('lang/it'), 0755, true, true);
        File::put(resource_path('lang/en/app.php'), $this->getTestStub('import/import-input-en-simple'));
        File::put(resource_path('lang/it/app.php'), $this->getTestStub('import/import-input-it-simple'));
        
        $result = $this->artisan('larex:import')
            ->expectsOutput('Importing entries...')
            ->expectsOutput("The '{$this->file}' already exists.")
            ->run();
        
        self::assertFileExists(base_path($this->file));
        self::assertNotEquals(
            $this->getTestStub('import/import-output-simple'),
            File::get(base_path($this->file))
        );
        self::assertEquals(1, $result);
    }
    
    public function test_larex_import_command_with_force(): void
    {
        $this->artisan('larex:init')->run();
        
        File::makeDirectory(resource_path('lang/en'), 0755, true, true);
        File::makeDirectory(resource_path('lang/it'), 0755, true, true);
        File::put(resource_path('lang/en/app.php'), $this->getTestStub('import/import-input-en-simple'));
        File::put(resource_path('lang/it/app.php'), $this->getTestStub('import/import-input-it-simple'));
        
        $result = $this->artisan('larex:import -f')
            ->expectsOutput('Importing entries...')
            ->expectsOutput('Files imported successfully.')
            ->run();
        
        self::assertFileExists(base_path($this->file));
        self::assertEquals($this->getTestStub('import/import-output-simple'), File::get(base_path($this->file)));
        self::assertEquals(0, $result);
    }
    
    public function providerImportCommand(): array
    {
        return [
            'simple' => ['import/import-input-en-simple', 'import/import-input-it-simple', 'import/import-output-simple'],
            'complex' => ['import/import-input-en-complex', 'import/import-input-it-complex', 'import/import-output-complex']
        ];
    }
}