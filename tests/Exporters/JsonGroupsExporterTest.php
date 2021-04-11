<?php

namespace Lukasss93\Larex\Tests\Exporters;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;
use Lukasss93\Larex\Tests\TestCase;

class JsonGroupsExporterTest extends TestCase
{
    public function test_exporter(): void
    {
        $this->initFromStub('exporters.json-groups.base.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('resources/lang/en/app.json created successfully.')
            ->expectsOutput('resources/lang/en/special.json created successfully.')
            ->expectsOutput('resources/lang/it/app.json created successfully.')
            ->expectsOutput('resources/lang/it/special.json created successfully.')
            ->assertExitCode(0);

        self::assertFileExists(resource_path('lang/en/app.json'));
        self::assertFileExists(resource_path('lang/en/special.json'));
        self::assertFileExists(resource_path('lang/it/app.json'));
        self::assertFileExists(resource_path('lang/it/special.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-en-app'),
            File::get(resource_path('lang/en/app.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-en-special'),
            File::get(resource_path('lang/en/special.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-it-app'),
            File::get(resource_path('lang/it/app.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-it-special'),
            File::get(resource_path('lang/it/special.json'))
        );
    }

    public function test_exporter_with_watch(): void
    {
        $this->initFromStub('exporters.json-groups.base.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group', '--watch' => true])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('resources/lang/en/app.json created successfully.')
            ->expectsOutput('resources/lang/en/special.json created successfully.')
            ->expectsOutput('resources/lang/it/app.json created successfully.')
            ->expectsOutput('resources/lang/it/special.json created successfully.')
            ->expectsOutput('Waiting for changes...')
            ->assertExitCode(0);

        self::assertFileExists(resource_path('lang/en/app.json'));
        self::assertFileExists(resource_path('lang/en/special.json'));
        self::assertFileExists(resource_path('lang/it/app.json'));
        self::assertFileExists(resource_path('lang/it/special.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-en-app'),
            File::get(resource_path('lang/en/app.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-en-special'),
            File::get(resource_path('lang/en/special.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-it-app'),
            File::get(resource_path('lang/it/app.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-it-special'),
            File::get(resource_path('lang/it/special.json'))
        );
    }

    public function test_exporter_with_include(): void
    {
        $this->initFromStub('exporters.json-groups.base.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group', '--include' => 'en'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('resources/lang/en/app.json created successfully.')
            ->expectsOutput('resources/lang/en/special.json created successfully.')
            ->assertExitCode(0);

        self::assertFileExists(resource_path('lang/en/app.json'));
        self::assertFileExists(resource_path('lang/en/special.json'));
        self::assertFileDoesNotExist(resource_path('lang/it/app.json'));
        self::assertFileDoesNotExist(resource_path('lang/it/special.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-en-app'),
            File::get(resource_path('lang/en/app.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-en-special'),
            File::get(resource_path('lang/en/special.json'))
        );
    }

    public function test_exporter_with_exclude(): void
    {
        $this->initFromStub('exporters.json-groups.base.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group', '--exclude' => 'en'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('resources/lang/it/app.json created successfully.')
            ->expectsOutput('resources/lang/it/special.json created successfully.')
            ->assertExitCode(0);

        self::assertFileDoesNotExist(resource_path('lang/en/app.json'));
        self::assertFileDoesNotExist(resource_path('lang/en/special.json'));
        self::assertFileExists(resource_path('lang/it/app.json'));
        self::assertFileExists(resource_path('lang/it/special.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-it-app'),
            File::get(resource_path('lang/it/app.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.base.output-it-special'),
            File::get(resource_path('lang/it/special.json'))
        );
    }

    public function test_exporter_with_warning(): void
    {
        $this->initFromStub('exporters.json-groups.warnings.input');

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('Missing key name at line 4. The row will be skipped.')
            ->expectsOutput('app.second at line 5, column 3 (en) is missing. It will be skipped.')
            ->expectsOutput('resources/lang/en/app.json created successfully.')
            ->expectsOutput('resources/lang/it/app.json created successfully.')
            ->assertExitCode(0);

        self::assertFileExists(resource_path('lang/en/app.json'));
        self::assertFileExists(resource_path('lang/it/app.json'));

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.warnings.output-en-app'),
            File::get(resource_path('lang/en/app.json'))
        );

        self::assertEquals(
            $this->getTestStub('exporters.json-groups.warnings.output-it-app'),
            File::get(resource_path('lang/it/app.json'))
        );
    }

    public function test_exporter_with_no_entries(): void
    {
        $this->artisan(LarexInitCommand::class)->run();

        $this->artisan(LarexExportCommand::class, ['exporter' => 'json:group'])
            ->expectsOutput("Processing the '$this->file' file...")
            ->expectsOutput('No entries exported.')
            ->assertExitCode(0);
    }
}
