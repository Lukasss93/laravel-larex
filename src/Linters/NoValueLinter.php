<?php

namespace Lukasss93\Larex\Linters;

use Illuminate\Support\Str;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;

class NoValueLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Checking missing values...';
    }

    /**
     * @inheritDoc
     */
    public function handle(CsvReader $reader): void
    {
        $errors = collect([]);

        $reader->getRows()->each(function ($columns, $line) use ($errors) {
            $line += 2;

            $columns->skip(2)->each(function ($value, $lang) use ($columns, $line, $errors) {
                if ($value === '') {
                    $column = array_search($lang, array_keys($columns->toArray()), true) + 1;
                    $errors->push("row {$line} ({$columns['group']}.{$columns['key']}), column {$column} ({$lang})");
                }
            });
        });

        if ($errors->isNotEmpty()) {
            $subject = Str::plural('value', $errors->count());
            throw new LintException("{$errors->count()} missing {$subject} found:", $errors->toArray());
        }
    }
}
