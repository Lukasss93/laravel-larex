<?php


namespace Lukasss93\Larex\Linters;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Utils;

class DuplicateValueLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return 'Checking duplicated values in the same row...';
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
            
            $duplicates = Utils::getDuplicateValues(collect($columns)->skip(2));
            
            if (count($duplicates) > 0) {
                $row = $rowN + 1;
                $message = "row {$row} ({$group}.{$key}), columns: ";
                
                foreach ($duplicates as $duplicate => $positions) {
                    foreach ($positions as $p => $position) {
                        $column = $position + 1;
                        $lang = $header[$position];
                        $message .= "{$column} ({$lang})";
                        
                        if ($p < count($positions) - 1) {
                            $message .= ', ';
                        }
                    }
                }
                
                $errors->push($message);
            }
        });
        
        if ($errors->isNotEmpty()) {
            $subject = Str::plural('value', $errors->count());
            throw new LintException("{$errors->count()} duplicate {$subject} found:", $errors->toArray());
        }
    }
}