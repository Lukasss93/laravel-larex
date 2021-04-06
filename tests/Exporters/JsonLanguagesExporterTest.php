<?php

namespace Lukasss93\Larex\Tests\Exporters;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Tests\TestCase;

class JsonLanguagesExporterTest extends TestCase
{
    public function test_exporter(): void
    {
        $this->initFromStub('exporters.json-language.base.input');

        $result = $this->artisan('larex:export json:lang')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en.json created successfully.")
            ->expectsOutput("resources/lang/it.json created successfully.")
            ->run();

        self::assertFileExists(resource_path('lang/en.json'));
        self::assertFileExists(resource_path('lang/it.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-language.base.output-en'),
            File::get(resource_path('lang/en.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-language.base.output-it'),
            File::get(resource_path('lang/it.json'))
        );

        self::assertEquals(0, $result);
    }

    public function test_exporter_with_watch(): void
    {
        $this->initFromStub('exporters.json-language.base.input');

        $result = $this->artisan('larex:export json:lang --watch')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en.json created successfully.")
            ->expectsOutput("resources/lang/it.json created successfully.")
            ->expectsOutput('Waiting for changes...')
            ->run();

        self::assertFileExists(resource_path('lang/en.json'));
        self::assertFileExists(resource_path('lang/it.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-language.base.output-en'),
            File::get(resource_path('lang/en.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-language.base.output-it'),
            File::get(resource_path('lang/it.json'))
        );

        self::assertEquals(0, $result);
    }

    public function test_exporter_with_include(): void
    {
        $this->initFromStub('exporters.json-language.include-exclude.input');

        $result = $this->artisan('larex:export json:lang --include=en')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en.json created successfully.")
            ->run();

        self::assertFileExists(resource_path('lang/en.json'));
        self::assertFileDoesNotExist(resource_path('lang/it.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-language.include-exclude.output-en'),
            File::get(resource_path('lang/en.json'))
        );

        self::assertEquals(0, $result);
    }

    public function test_exporter_with_exclude(): void
    {
        $this->initFromStub('exporters.json-language.include-exclude.input');

        $result = $this->artisan('larex:export json:lang --exclude=en')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/it.json created successfully.")
            ->run();

        self::assertFileDoesNotExist(resource_path('lang/en.json'));
        self::assertFileExists(resource_path('lang/it.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-language.include-exclude.output-it'),
            File::get(resource_path('lang/it.json'))
        );

        self::assertEquals(0, $result);
    }

    public function test_exporter_with_warning(): void
    {
        $this->initFromStub('exporters.json-language.warnings.input');

        $result = $this->artisan('larex:export json:lang')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('Invalid row at line 2. The row will be skipped.')
            ->expectsOutput('Missing key name at line 3. The row will be skipped.')
            ->expectsOutput('app.zero at line 4, column 3 (en) is missing. It will be skipped.')
            ->expectsOutput('resources/lang/en.json created successfully.')
            ->expectsOutput('resources/lang/it.json created successfully.')
            ->run();

        self::assertFileExists(resource_path('lang/en.json'));
        self::assertFileExists(resource_path('lang/it.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-language.warnings.output-en'),
            File::get(resource_path('lang/en.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-language.warnings.output-it'),
            File::get(resource_path('lang/it.json'))
        );

        self::assertEquals(0, $result);
    }

    public function test_exporter_with_no_entries(): void
    {
        $this->artisan('larex:init')->run();

        $result = $this->artisan('larex:export json:lang')
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('No entries exported.')
            ->run();

        self::assertEquals(0, $result);
    }
}
