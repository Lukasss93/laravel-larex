<?php


namespace Lukasss93\Larex\Linters;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lukasss93\Larex\Exceptions\LintException;

class NoValueLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return 'Checking missing values...';
    }
    
    /**
     * @inheritDoc
     */
    public function handle(Collection $rows): void
    {
        $header = $rows->first();
        
        $errors = collect([]);
        
        $rows->skip(1)->each(function ($columns, $rowN) use ($header, $errors) {
            [$group, $key] = $columns;
            
            collect($columns)->skip(2)->each(function ($cell, $columnN) use ($errors, $header, $group, $key, $rowN) {
                if ($cell === '') {
                    $row = $rowN + 1;
                    $column = $columnN + 1;
                    $lang = $header[$columnN];
                    
                    $errors->push("row {$row} ({$group}.{$key}), column {$column} ({$lang})");
                }
            });
        });
        
        if ($errors->isNotEmpty()) {
            $subject = Str::plural('value', $errors->count());
            throw new LintException("{$errors->count()} missing {$subject} found:", $errors->toArray());
        }
    }
}