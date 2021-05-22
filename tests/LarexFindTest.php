<?php

namespace Lukasss93\Larex\Tests;

use Lukasss93\Larex\Console\LarexFindCommand;

class LarexFindTest extends TestCase
{
    public function test_find_command_no_input(): void
    {
        $this->artisan(LarexFindCommand::class, ['value' => 'test'])
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init or php artisan larex:import')
            ->assertExitCode(1);
    }

    public function test_find_command_no_string_found(): void
    {
        $this->initFromStub('find.input');

        $this->artisan(LarexFindCommand::class, ['value' => 'test'])
            ->expectsOutput('No string found.')
            ->assertExitCode(0);
    }

    public function test_find_command_string_found(): void
    {
        $this->initFromStub('find.input');

        $this->artisan(LarexFindCommand::class, ['value' => 'app'])
            ->expectsTable([
                'group', 'key',
            ], [
                ['app', 'car'],
                ['app', 'apple'],
            ])
            ->assertExitCode(0);
    }
}
