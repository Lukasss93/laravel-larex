<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Console\LarexInitCommand;
use Lukasss93\Larex\Support\Utils;

class LarexInitTest extends TestCase
{
    public function test_init_command(): void
    {
        $this->artisan(LarexInitCommand::class)
            ->expectsOutput($this->file . ' created successfully.')
            ->assertExitCode(0);

        self::assertFileExists(base_path($this->file));
        self::assertEquals(Utils::getStub('default'), File::get(base_path($this->file)));
    }

    public function test_init_command_with_base_option(): void
    {
        $this->artisan(LarexInitCommand::class,['--base'=>true])
            ->expectsOutput($this->file . ' created successfully.')
            ->assertExitCode(0);

        self::assertFileExists(base_path($this->file));
        self::assertEquals(Utils::getStub('base'), File::get(base_path($this->file)));
    }

    public function test_init_command_fail_if_file_already_exists(): void
    {
        $this->artisan(LarexInitCommand::class)->run();

        $this->artisan(LarexInitCommand::class)
            ->expectsOutput($this->file . ' already exists.')
            ->assertExitCode(1);

        self::assertFileExists(base_path($this->file));
    }
}
