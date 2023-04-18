<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Foundation\Application;
use Lukasss93\Larex\LarexServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\CollectionMacros\CollectionMacroServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            CollectionMacroServiceProvider::class,
            LarexServiceProvider::class,
        ];
    }
}
