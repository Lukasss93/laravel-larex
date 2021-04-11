<?php

namespace Lukasss93\Larex\Linters;

use Illuminate\Support\Str;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\Utils;

class ValidHtmlValueLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Checking valid html values...';
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
                if (!Utils::isValidHTML($value)) {
                    $column = array_search($lang, array_keys($columns->toArray()), true) + 1;
                    $errors->push("line {$line} ({$columns['group']}.{$columns['key']}), column: {$column} ({$lang})");
                }
            });
        });

        if ($errors->isNotEmpty()) {
            $subject = Str::plural('string', $errors->count());
            throw new LintException("{$errors->count()} invalid html {$subject} found:", $errors->toArray());
        }
    }
}
