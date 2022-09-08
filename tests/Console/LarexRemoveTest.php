<?php

use Lukasss93\Larex\Console\LarexRemoveCommand;

it('removes rows', function () {
    initFromStub('remove.base.input');

    $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car'])
        ->expectsOutput('1 string found to remove:')
        ->expectsOutput('└ app.car')
        ->expectsQuestion('Are you sure you want to delete 1 string?', true)
        ->expectsOutput('Removed successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('remove.base.output');
});

it('removes rows with --force option', function () {
    initFromStub('remove.base.input');

    $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car', '--force' => true])
        ->expectsOutput('1 string found to remove:')
        ->expectsOutput('└ app.car')
        ->expectsOutput('Removed successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('remove.base.output');
});

it('does not remove rows due to user abort', function () {
    initFromStub('remove.abort.input');

    $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car'])
        ->expectsOutput('1 string found to remove:')
        ->expectsOutput('└ app.car')
        ->expectsQuestion('Are you sure you want to delete 1 string?', false)
        ->expectsOutput('Aborted.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('remove.abort.output');
});

it('does not remove rows due to no strings found', function () {
    initFromStub('remove.nostrings.input');

    $this->artisan(LarexRemoveCommand::class, ['key' => 'app.foo'])
        ->expectsOutput('No strings found to remove.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('remove.nostrings.output');
});

it('removes rows with wildcard', function () {
    initFromStub('remove.wildcard.input');

    $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car*'])
        ->expectsOutput('2 strings found to remove:')
        ->expectsOutput('├ app.car')
        ->expectsOutput('└ app.carrier')
        ->expectsQuestion('Are you sure you want to delete 2 strings?', true)
        ->expectsOutput('Removed successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('remove.wildcard.output');
});

it('does not remove rows due to missing localization file', function () {
    $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car'])
        ->expectsOutput(sprintf("The '%s' does not exists.", csv_path(true)))
        ->expectsOutput('Please create it with: php artisan larex:init')
        ->assertExitCode(1);

    expect(csv_path())->not->toBeFile();
});

it('removes rows and export data', function () {
    initFromStub('remove.export.input');

    $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car', '--export' => true])
        ->expectsOutput('1 string found to remove:')
        ->expectsOutput('└ app.car')
        ->expectsQuestion('Are you sure you want to delete 1 string?', true)
        ->expectsOutput('Removed successfully.')
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf('%s created successfully.', lang_rpath('en/app.php')))
        ->expectsOutput(sprintf('%s created successfully.', lang_rpath('it/app.php')))
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('remove.export.output')
        //check exported en file
        ->and(lang_path('en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('remove.export.output-app-en')
        //check exported it file
        ->and(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('remove.export.output-app-it');
});
