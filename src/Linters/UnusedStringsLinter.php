<?php

namespace Lukasss93\Larex\Linters;

use Illuminate\Support\Str;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\Utils;

class UnusedStringsLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Searching unused strings...';
    }

    /**
     * @inheritDoc
     */
    public function handle(CsvReader $reader): void
    {
        $filesFound = Utils::findFiles(config('larex.search.dirs'), config('larex.search.patterns'));
        $stringsFound = Utils::parseStrings($filesFound, config('larex.search.functions'))->pluck('string');

        $stringsUnused = $reader
            ->getRows()
            ->reject(fn($item) => $stringsFound->contains("{$item['group']}.{$item['key']}"))
            ->collect();

        if ($stringsUnused->isNotEmpty()) {
            $errors = collect([]);

            foreach ($stringsUnused as $i => $item) {
                $line = $i + 2;
                $errors->push("{$item['group']}.{$item['key']} is unused at line $line");
            }

            $subject = Str::plural('string', $errors->count());
            throw new LintException("{$errors->count()} unused $subject found:", $errors->toArray());
        }
    }
}
