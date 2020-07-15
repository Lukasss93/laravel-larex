<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;

class LarexSortTest extends TestCase
{
    public function test_sort_command(): void
    {
        $this->artisan('larex:init')->run();
        
        File::append(base_path($this->file), $this->getTestStub('sort-input'));
        
        $this->artisan('larex:sort')
            ->expectsOutput('Sorting che CSV rows...')
            ->expectsOutput('Sorting completed.')
            ->run();
        
        self::assertEquals($this->getTestStub('sort-output'), File::get(base_path($this->file)));
    }
}