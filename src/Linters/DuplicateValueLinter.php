<?php

namespace Lukasss93\Larex\Linters;

use Illuminate\Support\Str;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\Larex\Support\Utils;

class DuplicateValueLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Checking duplicated values in the same row...';
    }

    /**
     * @inheritDoc
     */
    public function handle(CsvReader $reader): void
    {
        $errors = collect([]);

        $reader->getRows()->each(function ($columns, $line) use ($errors) {
            $line += 2;

            $duplicates = Utils::getDuplicateValues($columns->skip(2));

            if (count($duplicates) > 0) {
                $message = "row $line ({$columns['group']}.{$columns['key']}), columns: ";

                foreach ($duplicates as $positions) {
                    foreach ($positions as $p => $lang) {
                        $column = array_search($lang, array_keys($columns->toArray()), true) + 1;
                        $message .= "$column ($lang)";

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
            throw new LintException("{$errors->count()} duplicate $subject found:", $errors->toArray());
        }
    }
}
