<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexRemoveCommand;

class LarexRemoveTest extends TestCase
{
    public function test_insert_command(): void
    {
        $this->initFromStub('remove.base.input');

        $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car'])
            ->expectsOutput('1 string found to remove:')
            ->expectsOutput('└ app.car')
            ->expectsQuestion('Are you sure you want to delete 1 string?', true)
            ->expectsOutput('Removed successfully.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('remove.base.output'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_with_force(): void
    {
        $this->initFromStub('remove.base.input');

        $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car', '--force' => true])
            ->expectsOutput('1 string found to remove:')
            ->expectsOutput('└ app.car')
            ->expectsOutput('Removed successfully.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('remove.base.output'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_with_abort(): void
    {
        $this->initFromStub('remove.abort.input');

        $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car'])
            ->expectsOutput('1 string found to remove:')
            ->expectsOutput('└ app.car')
            ->expectsQuestion('Are you sure you want to delete 1 string?', false)
            ->expectsOutput('Aborted.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('remove.abort.output'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_no_string_found(): void
    {
        $this->initFromStub('remove.nostrings.input');

        $this->artisan(LarexRemoveCommand::class, ['key' => 'app.foo'])
            ->expectsOutput('No strings found to remove.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('remove.nostrings.output'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_with_wildcard(): void
    {
        $this->initFromStub('remove.wildcard.input');

        $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car*'])
            ->expectsOutput('2 strings found to remove:')
            ->expectsOutput('├ app.car')
            ->expectsOutput('└ app.carrier')
            ->expectsQuestion('Are you sure you want to delete 2 strings?', true)
            ->expectsOutput('Removed successfully.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('remove.wildcard.output'),
            File::get(base_path($this->file))
        );
    }

    public function test_insert_command_if_file_does_not_exists(): void
    {
        $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car'])
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init')
            ->assertExitCode(1);
    }

    public function test_insert_command_with_export(): void
    {
        $this->initFromStub('remove.export.input');

        $this->artisan(LarexRemoveCommand::class, ['key' => 'app.car', '--export' => true])
            ->expectsOutput('1 string found to remove:')
            ->expectsOutput('└ app.car')
            ->expectsQuestion('Are you sure you want to delete 1 string?', true)
            ->expectsOutput('Removed successfully.')
            ->expectsOutput("Processing the '".config('larex.csv.path')."' file...")
            ->expectsOutput('resources/lang/en/app.php created successfully.')
            ->expectsOutput('resources/lang/it/app.php created successfully.')
            ->assertExitCode(0);

        self::assertEquals(
            $this->getTestStub('remove.export.output'),
            File::get(base_path($this->file))
        );

        self::assertEquals(
            $this->getTestStub('remove.export.output'),
            File::get(base_path($this->file))
        );

        self::assertEquals(
            $this->getTestStub('remove.export.output-app-en', config('larex.eol')),
            File::get(resource_path('lang/en/app.php'))
        );

        self::assertEquals(
            $this->getTestStub('remove.export.output-app-it', config('larex.eol')),
            File::get(resource_path('lang/it/app.php'))
        );
    }
}
