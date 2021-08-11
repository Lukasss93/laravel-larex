<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Foundation\Application;
use Lukasss93\Larex\LarexServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

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
            LarexServiceProvider::class,
        ];
    }
}
