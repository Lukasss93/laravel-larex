<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\LarexServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected $file = 'resources' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'localization.csv';
    
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
        
        $this->beforeApplicationDestroyed(function() {
            if(File::exists(base_path($this->file))) {
                File::delete(base_path($this->file));
            }
        });
    }
}