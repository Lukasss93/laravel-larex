<?php


namespace Lukasss93\Larex\Linters;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Utils;

class UntranslatedStringsLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return 'Searching untranslated strings...';
    }
    
    /**
     * @inheritDoc
     */
    public function handle(Collection $rows): void
    {
        $filesFound=Utils::findFiles(config('larex.search.dirs'), config('larex.search.patterns'));
        $stringsFound = Utils::parseStrings($filesFound, config('larex.search.functions'));
        
        $stringsSaved = $rows
            ->skip(1)
            ->map(function ($item) {
                return "{$item[0]}.{$item[1]}";
            })->values();
        
        $stringsUntranslated = $stringsFound->reject(function ($item) use ($stringsSaved) {
            return $stringsSaved->contains($item['string']);
        })->groupBy('filename')
            ->map->sortBy('line')
            ->map->values()
            ->flatten(1);
        
        if ($stringsUntranslated->isNotEmpty()) {
            $errors = collect([]);
            
            foreach ($stringsUntranslated as $item) {
                $errors->push("{$item['string']} is untranslated at line {$item['line']}, column {$item['column']} in {$item['filepath']}");
            }
            
            $subject = Str::plural('string', $errors->count());
            throw new LintException("{$errors->count()} untranslated {$subject} found:", $errors->toArray());
        }
    }
    
    
}