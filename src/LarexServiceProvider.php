<?php

namespace Lukasss93\Larex;

use Illuminate\Support\ServiceProvider;
use Lukasss93\Larex\Console\LarexCommand;
use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;
use Lukasss93\Larex\Console\LarexInsertCommand;
use Lukasss93\Larex\Console\LarexSortCommand;

class LarexServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $configPath = __DIR__ . '/config/larex.php';
        $this->mergeConfigFrom($configPath, 'larex');

        $this->commands([
            LarexInitCommand::class,
            LarexCommand::class,
            LarexExportCommand::class,
            LarexImportCommand::class,
            LarexSortCommand::class,
            LarexInsertCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
    }
}
