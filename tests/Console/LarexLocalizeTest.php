<?php

use Lukasss93\Larex\Console\LarexLocalizeCommand;

it('does not find unlocalized strings', function () {
    initFromStub('localize.no-strings.blade', resource_path('views/test.blade.php'));
    initFromStub('localize.no-strings.csv');

    $this->artisan(LarexLocalizeCommand::class)
        ->expectsOutput('No unlocalized strings found.')
        ->assertExitCode(0);
});

it('finds unlocalized strings', function () {
    $testFilePath = initFromStub('localize.with-strings.blade', resource_path('views/test.blade.php'));
    initFromStub('localize.with-strings.csv');

    $this->artisan(LarexLocalizeCommand::class)
        ->expectsOutput('1 unlocalized string found:')
        ->expectsOutput(sprintf('app.news is untranslated at line 90, column 63 in %s', $testFilePath))
        ->assertExitCode(0);
});

it('finds unlocalized strings and import data', function () {
    $testFilePath = initFromStub('localize.with-strings-import.blade', resource_path('views/test.blade.php'));
    initFromStub('localize.with-strings-import.csv-before');

    $this->artisan(LarexLocalizeCommand::class, ['--import' => true])
        ->expectsOutput('1 unlocalized string found:')
        ->expectsOutput("app.news is untranslated at line 90, column 63 in $testFilePath")
        ->expectsOutput('')
        ->expectsOutput('Adding unlocalized string to CSV file...')
        ->expectsOutput('Done.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('localize.with-strings-import.csv-after');
});
