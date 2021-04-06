<?php

namespace Lukasss93\Larex\Tests\Importers;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Tests\TestCase;

class LaravelImporterTest extends TestCase
{
    public function test_importer(): void
    {
        File::makeDirectory(resource_path('lang/en'), 0755, true, true);
        File::makeDirectory(resource_path('lang/it'), 0755, true, true);
        File::put(resource_path('lang/en/complex.php'), $this->getTestStub('importers.laravel.input-en-complex'));
        File::put(resource_path('lang/en/simple.php'), $this->getTestStub('importers.laravel.input-en-simple'));
        File::put(resource_path('lang/it/complex.php'), $this->getTestStub('importers.laravel.input-it-complex'));
        File::put(resource_path('lang/it/simple.php'), $this->getTestStub('importers.laravel.input-it-simple'));

        $this->artisan(LarexImportCommand::class, ['importer' => 'laravel'])
            ->expectsOutput('Importing entries...')
            ->expectsOutput('Data imported successfully.')
            ->assertExitCode(0);

        self::assertFileExists(base_path($this->file));
        self::assertEquals($this->getTestStub('importers.laravel.output'), File::get(base_path($this->file)));
    }
}
