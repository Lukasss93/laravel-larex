<?php

namespace Lukasss93\Larex;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexFindCommand;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;
use Lukasss93\Larex\Console\LarexInsertCommand;
use Lukasss93\Larex\Console\LarexLangAddCommand;
use Lukasss93\Larex\Console\LarexLangOrderCommand;
use Lukasss93\Larex\Console\LarexLangRemoveCommand;
use Lukasss93\Larex\Console\LarexLintCommand;
use Lukasss93\Larex\Console\LarexLocalizeCommand;
use Lukasss93\Larex\Console\LarexRemoveCommand;
use Lukasss93\Larex\Console\LarexSortCommand;

class LarexServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPublishables();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/larex.php', 'larex');

        $this->registerCommands();

        Collection::macro('insertAt', function (int $index, $item, $key = null) {
            $after = $this->splice($index);
            $this->items = isset($key)
                ? $this->put($key, $item)->merge($after)->toArray()
                : $this->push($item)->merge($after)->toArray();

            return $this;
        });
    }

    protected function registerCommands(): void
    {
        $this->commands([
            LarexInitCommand::class,
            LarexExportCommand::class,
            LarexImportCommand::class,
            LarexSortCommand::class,
            LarexInsertCommand::class,
            LarexLintCommand::class,
            LarexLocalizeCommand::class,
            LarexFindCommand::class,
            LarexRemoveCommand::class,
            LarexLangAddCommand::class,
            LarexLangRemoveCommand::class,
            LarexLangOrderCommand::class,
        ]);
    }

    protected function registerPublishables(): void
    {
        $this->publishes([
            __DIR__.'/config/larex.php' => config_path('larex.php'),
        ], 'larex-config');
    }
}
