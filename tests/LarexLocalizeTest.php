<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexLocalizeCommand;

class LarexLocalizeTest extends TestCase
{
    public function test_localize_command_no_strings_found(): void
    {
        $testFilePath = resource_path('views/test.blade.php');
        File::put($testFilePath, $this->getTestStub('localize.no-strings.blade'));

        $this->initFromStub('localize.no-strings.csv');

        $this->artisan(LarexLocalizeCommand::class)
            ->expectsOutput('No unlocalized strings found.')
            ->assertExitCode(0);
    }

    public function test_localize_command_with_strings_found(): void
    {
        $testFilePath = resource_path('views'.DIRECTORY_SEPARATOR.'test.blade.php');
        File::put($testFilePath, $this->getTestStub('localize.with-strings.blade'));

        $this->initFromStub('localize.with-strings.csv');

        $this->artisan(LarexLocalizeCommand::class)
            ->expectsOutput('1 unlocalized string found:')
            ->expectsOutput("app.news is untranslated at line 90, column 63 in $testFilePath")
            ->assertExitCode(0);
    }

    public function test_localize_command_with_strings_found_and_import(): void
    {
        $testFilePath = resource_path('views'.DIRECTORY_SEPARATOR.'test.blade.php');
        File::put($testFilePath, $this->getTestStub('localize.with-strings-import.blade'));

        $this->initFromStub('localize.with-strings-import.csv-before');

        $this->artisan(LarexLocalizeCommand::class, ['--import' => true])
            ->expectsOutput('1 unlocalized string found:')
            ->expectsOutput("app.news is untranslated at line 90, column 63 in $testFilePath")
            ->expectsOutput('')
            ->expectsOutput('Adding unlocalized string to CSV file...')
            ->expectsOutput('Done.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('localize.with-strings-import.csv-after'),
            File::get(base_path(config('larex.csv.path')))
        );
    }
}
