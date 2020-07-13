<?php

namespace Lukasss93\Larex\Tests;

use Illuminate\Support\Facades\File;

class LarexSortTest extends TestCase
{
    public function test_sort_command(): void
    {
        $this->artisan('larex:init')->run();
        
        $inputData = <<<CSV
bbb;abc;123
aaa;cba;123
aaa;bca;123

CSV;
        
        File::append(base_path($this->file), $inputData);
        
        $this->artisan('larex:sort')
            ->expectsOutput('Sorting che CSV rows...')
            ->expectsOutput('Sorting completed.')
            ->run();
        
        $outputData = <<<CSV
group;key;en
aaa;bca;123
aaa;cba;123
bbb;abc;123

CSV;
        
        self::assertEquals($outputData, File::get(base_path($this->file)));
    }
}