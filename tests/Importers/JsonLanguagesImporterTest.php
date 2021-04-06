<?php

namespace Lukasss93\Larex\Tests\Importers;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Tests\TestCase;

class JsonLanguagesImporterTest extends TestCase
{
    public function test_importer(): void
    {
        File::makeDirectory(resource_path('lang/en'), 0755, true, true);
        File::makeDirectory(resource_path('lang/it'), 0755, true, true);
        File::put(resource_path('lang/en.json'), $this->getTestStub('importers.json-langs.input-en'));
        File::put(resource_path('lang/it.json'), $this->getTestStub('importers.json-langs.input-it'));

        $this->artisan(LarexImportCommand::class, ['importer' => 'json:lang'])
            ->expectsOutput('Importing entries...')
            ->expectsOutput('Data imported successfully.')
            ->assertExitCode(0);

        self::assertFileExists(base_path($this->file));
        self::assertEquals($this->getTestStub('importers.json-langs.output'), File::get(base_path($this->file)));
    }
}
