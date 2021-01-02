<?php


namespace Lukasss93\Larex\Linters;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lukasss93\Larex\Exceptions\LintException;

class DuplicateKeyLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return 'Checking duplicated keys...';
    }
    
    /**
     * @inheritDoc
     */
    public function handle(Collection $rows): void
    {
        $count = [];
        
        $rows->skip(1)->each(function ($columns, $rowN) use (&$count) {
            [$group, $key] = $columns;
            $count["{$group}.{$key}"][] = $rowN;
        });
        
        $duplicates = collect($count)->filter(function ($items) {
            return count($items) > 1;
        });
        
        if ($duplicates->isNotEmpty()) {
            $errors = collect([]);
            
            foreach ($duplicates as $duplicate => $positions) {
                $message = '';
                foreach ($positions as $p => $position) {
                    $message .= $position + 1;
                    
                    if ($p < count($positions) - 1) {
                        $message .= ', ';
                    }
                }
                $message .= " ({$duplicate})";
                $errors->push($message);
            }
            
            $subject = Str::plural('key', $duplicates->count());
            throw new LintException("{$duplicates->count()} duplicate {$subject} found:", $errors->toArray());
        }
    }
}