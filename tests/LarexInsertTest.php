<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexInsertCommand;

class LarexInsertTest extends TestCase
{
    public function test_insert_command(): void
    {
        $this->initFromStub('insert.base.input');

        $this->artisan(LarexInsertCommand::class)
            ->expectsQuestion('Enter the group', 'app')
            ->expectsQuestion('Enter the key', 'uncle')
            ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
            ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
            ->expectsQuestion('Are you sure?', 'yes')
            ->expectsOutput('Item added successfully.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('insert.base.output'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_with_special_chars(): void
    {
        $this->initFromStub('insert.special.input');

        $this->artisan(LarexInsertCommand::class)
            ->expectsQuestion('Enter the group', 'app')
            ->expectsQuestion('Enter the key', 'special')
            ->expectsQuestion('[1/2] Enter the value for [en] language', 'àèìòù')
            ->expectsQuestion('[2/2] Enter the value for [it] language', '"\'!£$%&/()=?*§<>;:_,.-#@ç[]{}°')
            ->expectsQuestion('Are you sure?', 'yes')
            ->expectsOutput('Item added successfully.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('insert.special.output'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_exists_continue_yes(): void
    {
        $this->initFromStub('insert.exists.input');

        $this->artisan(LarexInsertCommand::class)
            ->expectsQuestion('Enter the group', 'app')
            ->expectsQuestion('Enter the key', 'standard')
            ->expectsQuestion('<fg=red>The group/key pair already exists. Do you want to continue?</>', 'yes')
            ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
            ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
            ->expectsQuestion('Are you sure?', 'yes')
            ->expectsOutput('Item added successfully.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('insert.exists.output-continue-yes'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_exists_continue_no(): void
    {
        $this->initFromStub('insert.exists.input');

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

        self::assertEquals(
            $this->getTestStub('insert.exists.output-continue-no'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_if_file_does_not_exists(): void
    {
        $this->artisan(LarexInsertCommand::class)
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init')
            ->assertExitCode(1);
    }

    public function test_insert_command_with_group_and_key_empty(): void
    {
        $this->initFromStub('insert.base.input');

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

        self::assertEquals(
            $this->getTestStub('insert.base.output'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_with_export(): void
    {
        $this->initFromStub('insert.base.input');

        $this->artisan(LarexInsertCommand::class, ['--export' => true])
            ->expectsQuestion('Enter the group', 'app')
            ->expectsQuestion('Enter the key', 'uncle')
            ->expectsQuestion('[1/2] Enter the value for [en] language', 'Uncle')
            ->expectsQuestion('[2/2] Enter the value for [it] language', 'Zio')
            ->expectsQuestion('Are you sure?', 'yes')
            ->expectsOutput('Item added successfully.')
            ->expectsOutput('')
            ->expectsOutput("Processing the '".config('larex.csv.path')."' file...")
            ->expectsOutput('resources/lang/en/app.php created successfully.')
            ->expectsOutput('resources/lang/it/app.php created successfully.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('insert.base.output'),
            File::get(base_path($this->file))
        );

        self::assertEquals(
            $this->getTestStub('insert.base.output-app-en'),
            File::get(resource_path('lang/en/app.php'))
        );

        self::assertEquals(
            $this->getTestStub('insert.base.output-app-it'),
            File::get(resource_path('lang/it/app.php'))
        );
    }

    public function test_insert_command_with_correction(): void
    {
        $this->initFromStub('insert.correction.input');

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

        self::assertEquals(
            $this->getTestStub('insert.correction.output'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_with_correction_and_export(): void
    {
        $this->initFromStub('insert.correction.input');

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
            ->expectsOutput("Processing the '".config('larex.csv.path')."' file...")
            ->expectsOutput('resources/lang/en/app.php created successfully.')
            ->expectsOutput('resources/lang/it/app.php created successfully.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('insert.correction.output'),
            File::get(base_path($this->file))
        );

        self::assertEquals(
            $this->getTestStub('insert.correction.output-app-en'),
            File::get(resource_path('lang/en/app.php'))
        );

        self::assertEquals(
            $this->getTestStub('insert.correction.output-app-it'),
            File::get(resource_path('lang/it/app.php'))
        );
    }


}
