<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Lukasss93\Larex\LarexServiceProvider;
use Lukasss93\Larex\Support\Utils;
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

            //clear lang folder
            $items = glob(resource_path('lang/*'));
            foreach ($items as $item) {
                if(is_dir($item)){
                    File::deleteDirectory($item);
                } else {
                    File::delete($item);
                }
            }
        });
    }

    public function getTestStub(string $name): string
    {
        $name = str_replace('.', '/', $name);
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
