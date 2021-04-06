<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Support\Utils;

class LarexInitTest extends TestCase
{
    public function test_init_command(): void
    {
        $result = $this->artisan('larex:init')
            ->expectsOutput($this->file . ' created successfully.')
            ->run();

        self::assertFileExists(base_path($this->file));
        self::assertEquals(Utils::getStub('default'), File::get(base_path($this->file)));
        self::assertEquals(0, $result);
    }

    public function test_init_command_with_base_option(): void
    {
        $result = $this->artisan('larex:init --base')
            ->expectsOutput($this->file . ' created successfully.')
            ->run();

        self::assertFileExists(base_path($this->file));
        self::assertEquals(Utils::getStub('base'), File::get(base_path($this->file)));
        self::assertEquals(0, $result);
    }

    public function test_init_command_fail_if_file_already_exists(): void
    {
        $this->artisan('larex:init')->run();

        $result = $this->artisan('larex:init')
            ->expectsOutput($this->file . ' already exists.')
            ->run();

        self::assertFileExists(base_path($this->file));
        self::assertEquals(1, $result);
    }
}
