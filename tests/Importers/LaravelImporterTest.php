<?php

namespace Lukasss93\Larex\Tests\Importers;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Tests\TestCase;

class LaravelImporterTest extends TestCase
{
    public function test_importer_base(): void
    {
        File::makeDirectory(resource_path('lang/en'), 0755, true, true);
        File::makeDirectory(resource_path('lang/it'), 0755, true, true);
        File::put(resource_path('lang/en/complex.php'), $this->getTestStub('importers.laravel.base.input-en-complex'));
        File::put(resource_path('lang/en/simple.php'), $this->getTestStub('importers.laravel.base.input-en-simple'));
        File::put(resource_path('lang/it/complex.php'), $this->getTestStub('importers.laravel.base.input-it-complex'));
        File::put(resource_path('lang/it/simple.php'), $this->getTestStub('importers.laravel.base.input-it-simple'));

        $this->artisan(LarexImportCommand::class, ['importer' => 'laravel'])
            ->expectsOutput('Importing entries...')
            ->expectsOutput('Data imported successfully.')
            ->assertExitCode(0);

        self::assertFileExists(base_path($this->file));
        self::assertEquals($this->getTestStub('importers.laravel.base.output'), File::get(base_path($this->file)));
    }

    public function test_importer_territory(): void
    {
        File::makeDirectory(resource_path('lang/en_GB'), 0755, true, true);
        File::makeDirectory(resource_path('lang/it'), 0755, true, true);
        File::put(resource_path('lang/en_GB/complex.php'),
            $this->getTestStub('importers.laravel.territory.input-en_GB-complex'));
        File::put(resource_path('lang/en_GB/simple.php'),
            $this->getTestStub('importers.laravel.territory.input-en_GB-simple'));
        File::put(resource_path('lang/it/complex.php'),
            $this->getTestStub('importers.laravel.territory.input-it-complex'));
        File::put(resource_path('lang/it/simple.php'),
            $this->getTestStub('importers.laravel.territory.input-it-simple'));

        $this->artisan(LarexImportCommand::class, ['importer' => 'laravel'])
            ->expectsOutput('Importing entries...')
            ->expectsOutput('Data imported successfully.')
            ->assertExitCode(0);

        self::assertFileExists(base_path($this->file));
        self::assertEquals($this->getTestStub('importers.laravel.territory.output'), File::get(base_path($this->file)));
    }
}
