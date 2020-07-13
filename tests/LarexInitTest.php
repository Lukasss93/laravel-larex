<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Utils;

class LarexInitTest extends TestCase
{
    public function test_init_command(): void
    {
        $this->artisan('larex:init')
            ->expectsOutput($this->file . ' created successfully.')
            ->run();
        
        self::assertFileExists(base_path($this->file));
        self::assertEquals(Utils::getStub('base'), File::get(base_path($this->file)));
    }
    
    public function test_init_command_with_base_option(): void
    {
        $this->artisan('larex:init --base')
            ->expectsOutput($this->file . ' created successfully.')
            ->run();
        
        self::assertFileExists(base_path($this->file));
        self::assertEquals(Utils::getStub('laravel'), File::get(base_path($this->file)));
    }
    
    public function test_init_command_fail_if_file_already_exists(): void
    {
        $this->artisan('larex:init')->run();
        
        $this->artisan('larex:init')
            ->expectsOutput($this->file . ' already exists.')
            ->run();
        
        self::assertFileExists(base_path($this->file));
    }
}