<?php

namespace Lukasss93\Larex\Tests;

class LarexExportTest extends TestCase
{
    public function test_export_with_missing_file(): void
    {
        $result = $this->artisan('larex:export')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init or php artisan larex:import')
            ->run();

        self::assertEquals(1, $result);
    }

    public function test_export_with_include_exclude(): void
    {
        $this->artisan('larex:init')->run();

        $result = $this->artisan('larex:export --include= --exclude=')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("The --include and --exclude options can be used only one at a time.")
            ->run();

        self::assertEquals(1, $result);
    }

    public function test_export_with_missing_exporter(): void
    {
        $this->artisan('larex:init')->run();

        $result = $this->artisan('larex:export foo')
            ->expectsOutput("Exporter 'foo' not found.")
            ->expectsOutput('')
            ->expectsOutput('Available exporters:')
            ->expectsOutput('laravel - Export data from CSV to Laravel localization files')
            ->expectsOutput('json:lang - Export data from CSV to JSON by language')
            ->expectsOutput('json:group - Export data from CSV to JSON by group')
            ->expectsOutput('')
            ->run();

        self::assertEquals(1, $result);
    }

    public function test_export_with_invalid_exporter(): void
    {
        config(['larex.exporters.list.foo' => new class() {}]);

        $this->artisan('larex:init')->run();

        $result = $this->artisan('larex:export foo')
            ->expectsOutput("Exporter 'foo' must implements Lukasss93\Larex\Contracts\Exporter interface.")
            ->run();

        self::assertEquals(1, $result);
    }
}