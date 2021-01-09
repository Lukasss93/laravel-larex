<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\LarexServiceProvider;
use Lukasss93\Larex\Utils;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected $file;
    
    /**
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            LarexServiceProvider::class,
        ];
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->file = config('larex.csv.path');
        
        $this->afterApplicationCreated(function () {
            //set global csv settings
            config([
                'larex.csv' => [
                    'path' => 'resources/lang/localization.csv',
                    'delimiter' => ',',
                    'enclosure' => '"',
                    'escape' => '"',
                ],
                'larex.search' => [
                    'dirs' => ['resources/views'],
                    'patterns' => ['*.php'],
                    'functions' => ['__', 'trans', '@lang']
                ],
            ]);
            
            if (File::exists(resource_path('lang/en'))) {
                File::deleteDirectory(resource_path('lang/en'));
            }
            
            //delete csv file
            if (File::exists(base_path($this->file))) {
                File::delete(base_path($this->file));
            }
            
            //delete lang folders
            $folders = glob(resource_path('lang/*'), GLOB_ONLYDIR);
            foreach ($folders as $folder) {
                File::deleteDirectory($folder);
            }
        });
    }
    
    public function getTestStub(string $name): string
    {
        $content = file_get_contents(__DIR__ . '/Stubs/' . $name . '.stub');
        return Utils::normalizeEOLs($content);
    }
    
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        putenv('NOLOOP=1');
    }
    
    public function initFromStub(string $stub): void
    {
        File::put(base_path($this->file), $this->getTestStub($stub));
    }
}