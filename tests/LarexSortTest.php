<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;

class LarexSortTest extends TestCase
{
    public function test_sort_command(): void
    {
        $this->initFromStub('sort/sort-input');
        
        $this->artisan('larex:sort')
            ->expectsOutput('Sorting che CSV rows...')
            ->expectsOutput('Sorting completed.')
            ->run();
        
        self::assertEquals(
            $this->getTestStub('sort/sort-output'),
            File::get(base_path($this->file))
        );
    }
    
    public function test_sort_command_if_file_does_not_exists(): void
    {
        $this->artisan('larex:sort')
            ->expectsOutput('Sorting che CSV rows...')
            ->expectsOutput("The '$this->file' does not exists.")
            ->expectsOutput('Please create it with: php artisan larex:init')
            ->run();
    }
}