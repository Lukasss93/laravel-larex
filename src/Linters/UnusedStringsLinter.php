<?php

namespace Lukasss93\Larex\Linters;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Utils;

class UnusedStringsLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return 'Searching unused strings...';
    }
    
    /**
     * @inheritDoc
     */
    public function handle(Collection $rows): void
    {
        $filesFound=Utils::findFiles(config('larex.search.dirs'), config('larex.search.patterns'));
        $stringsFound = Utils::parseStrings($filesFound, config('larex.search.functions'))->pluck('string');
        $stringsSaved = $rows->skip(1);
        
        $stringsUnused = $stringsSaved->reject(function ($item) use ($stringsFound) {
            return $stringsFound->contains("{$item[0]}.{$item[1]}");
        });
        
        if ($stringsUnused->isNotEmpty()) {
            $errors = collect([]);
            
            foreach ($stringsUnused as $i => $item) {
                $line = $i + 1;
                $errors->push("{$item[0]}.{$item[1]} is unused at line {$line}");
            }
            
            $subject = Str::plural('string', $errors->count());
            throw new LintException("{$errors->count()} unused {$subject} found:", $errors->toArray());
        }
    }
}