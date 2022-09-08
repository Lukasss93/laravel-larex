<?php

use Lukasss93\Larex\Console\LarexInitCommand;
use Lukasss93\Larex\Console\LarexInsertCommand;

it('does not insert string due to missing localization file', function () {
    $this->artisan(LarexInsertCommand::class)
        ->expectsOutput(sprintf("The '%s' does not exists.", csv_path(true)))
        ->expectsOutput('Please create it with: php artisan larex:init')
        ->assertExitCode(1);
});

it('inserts string', function () {
    initFromStub('insert.base.input');

    $this->artisan(LarexInsertCommand::class)
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'uncle')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
        ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
        ->expectsQuestion('Are you sure?', 'yes')
        ->expectsOutput('Item added successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.base.output');
});

it('inserts string with special chars', function () {
    initFromStub('insert.special.input');

    $this->artisan(LarexInsertCommand::class)
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'special')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'àèìòù')
        ->expectsQuestion('[2/2] Enter the value for [it] language', '"\'!£$%&/()=?*§<>;:_,.-#@ç[]{}°')
        ->expectsQuestion('Are you sure?', 'yes')
        ->expectsOutput('Item added successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.special.output');
});

it('inserts string overwriting existent', function () {
    initFromStub('insert.exists.input');

    $this->artisan(LarexInsertCommand::class)
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'standard')
        ->expectsQuestion('<fg=red>The group/key pair already exists. Do you want to continue?</>', 'yes')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
        ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
        ->expectsQuestion('Are you sure?', 'yes')
        ->expectsOutput('Item added successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.exists.output-continue-yes');
});

it('inserts string not overwriting existent', function () {
    initFromStub('insert.exists.input');

    $this->artisan(LarexInsertCommand::class)
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'standard')
        ->expectsQuestion('<fg=red>The group/key pair already exists. Do you want to continue?</>', 'no')
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'uncle')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
        ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
        ->expectsQuestion('Are you sure?', 'yes')
        ->expectsOutput('Item added successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.exists.output-continue-no');
});

it('inserts string checking empty group/key', function () {
    initFromStub('insert.base.input');

    $this->artisan(LarexInsertCommand::class)
        ->expectsQuestion('Enter the group', '')
        ->expectsOutput('Please enter a group!')
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', '')
        ->expectsOutput('Please enter a key!')
        ->expectsQuestion('Enter the key', 'uncle')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
        ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
        ->expectsQuestion('Are you sure?', 'yes')
        ->expectsOutput('Item added successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.base.output');
});

it('inserts string and export data', function () {
    initFromStub('insert.base.input');

    $this->artisan(LarexInsertCommand::class, ['--export' => true])
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'uncle')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
        ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
        ->expectsQuestion('Are you sure?', 'yes')
        ->expectsOutput('Item added successfully.')
        ->expectsOutput('')
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf('%s created successfully.', lang_rpath('en/app.php')))
        ->expectsOutput(sprintf('%s created successfully.', lang_rpath('it/app.php')))
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.base.output')
        //check exported en file
        ->and(lang_path('en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.base.output-app-en')
        //check exported it file
        ->and(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.base.output-app-it');
});

it('inserts string with correction', function () {
    initFromStub('insert.correction.input');

    $this->artisan(LarexInsertCommand::class)
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'dad')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'Dad')
        ->expectsQuestion('[2/2] Enter the value for [it] language', 'Papa')
        ->expectsQuestion('Are you sure?', 'no')
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'dad')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'Dad')
        ->expectsQuestion('[2/2] Enter the value for [it] language', 'Papà')
        ->expectsQuestion('Are you sure?', 'yes')
        ->expectsOutput('Item added successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.correction.output');
});

it('inserts string with correction and export data', function () {
    initFromStub('insert.correction.input');

    $this->artisan(LarexInsertCommand::class, ['--export' => true])
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'dad')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'Dad')
        ->expectsQuestion('[2/2] Enter the value for [it] language', 'Papa')
        ->expectsQuestion('Are you sure?', 'no')
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'dad')
        ->expectsQuestion('[1/2] Enter the value for [en] language', 'Dad')
        ->expectsQuestion('[2/2] Enter the value for [it] language', 'Papà')
        ->expectsQuestion('Are you sure?', 'yes')
        ->expectsOutput('Item added successfully.')
        ->expectsOutput('')
        ->expectsOutput(sprintf("Processing the '%s' file...", csv_path(true)))
        ->expectsOutput(sprintf('%s created successfully.', lang_rpath('en/app.php')))
        ->expectsOutput(sprintf('%s created successfully.', lang_rpath('it/app.php')))
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.correction.output')
        //check exported en file
        ->and(lang_path('en/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.correction.output-app-en')
        //check exported it file
        ->and(lang_path('it/app.php'))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.correction.output-app-it');
});

it('inserts string after initializing localization file', function () {
    $this->artisan(LarexInitCommand::class);

    $this->artisan(LarexInsertCommand::class)
        ->expectsQuestion('Enter the group', 'app')
        ->expectsQuestion('Enter the key', 'hello')
        ->expectsQuestion('[1/1] Enter the value for [en] language', 'Hello!')
        ->expectsQuestion('Are you sure?', 'yes')
        ->expectsOutput('Item added successfully.')
        ->assertExitCode(0);

    expect(csv_path())
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('insert.init.output');
});
