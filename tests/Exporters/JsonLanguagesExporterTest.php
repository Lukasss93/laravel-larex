<?php

namespace Lukasss93\Larex\Tests\Exporters;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;
use Lukasss93\Larex\Tests\TestCase;

class JsonLanguagesExporterTest extends TestCase
{
    public function test_exporter(): void
    {
        $this->initFromStub('exporters.json-language.base.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en.json created successfully.")
            ->expectsOutput("resources/lang/it.json created successfully.")
            ->assertExitCode(0);

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
    }

    public function test_exporter_with_watch(): void
    {
        $this->initFromStub('exporters.json-language.base.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang', '--watch' => true])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en.json created successfully.")
            ->expectsOutput("resources/lang/it.json created successfully.")
            ->expectsOutput('Waiting for changes...')
            ->assertExitCode(0);

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
    }

    public function test_exporter_with_include(): void
    {
        $this->initFromStub('exporters.json-language.include-exclude.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang', '--include' => 'en'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/en.json created successfully.")
            ->assertExitCode(0);

        self::assertFileExists(resource_path('lang/en.json'));
        self::assertFileDoesNotExist(resource_path('lang/it.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-language.include-exclude.output-en'),
            File::get(resource_path('lang/en.json'))
        );
    }

    public function test_exporter_with_exclude(): void
    {
        $this->initFromStub('exporters.json-language.include-exclude.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang', '--exclude' => 'en'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput("resources/lang/it.json created successfully.")
            ->assertExitCode(0);

        self::assertFileDoesNotExist(resource_path('lang/en.json'));
        self::assertFileExists(resource_path('lang/it.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-language.include-exclude.output-it'),
            File::get(resource_path('lang/it.json'))
        );
    }

    public function test_exporter_with_warning(): void
    {
        $this->initFromStub('exporters.json-language.warnings.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('Missing key name at line 3. The row will be skipped.')
            ->expectsOutput('app.zero at line 4, column 3 (en) is missing. It will be skipped.')
            ->expectsOutput('resources/lang/en.json created successfully.')
            ->expectsOutput('resources/lang/it.json created successfully.')
            ->assertExitCode(0);

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
    }

    public function test_exporter_with_no_entries(): void
    {
        $this->artisan(LarexInitCommand::class)->run();

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:lang'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('No entries exported.')
            ->assertExitCode(0);
    }
}
