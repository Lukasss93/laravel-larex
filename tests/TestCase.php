<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\LarexServiceProvider;
use Lukasss93\Larex\Utils;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected $file = 'resources/lang/localization.csv';
    
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
        
        $this->afterApplicationCreated(function(){
            
            if(File::exists(resource_path('lang/en'))) {
                File::deleteDirectory(resource_path('lang/en'));
            }
    
            File::makeDirectory(resource_path('lang/en'), 0755, true, true);
        });
        
        $this->beforeApplicationDestroyed(function(){
            if(File::exists(base_path($this->file))) {
                File::delete(base_path($this->file));
            }
    
            if(File::exists(resource_path('lang/it'))) {
                File::deleteDirectory(resource_path('lang/it'));
            }
    
            if(File::exists(resource_path('lang/en'))) {
                File::deleteDirectory(resource_path('lang/en'));
            }
    
            File::makeDirectory(resource_path('lang/en'), 0755, true, true);
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
}