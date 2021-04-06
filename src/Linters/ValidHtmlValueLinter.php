<?php


namespace Lukasss93\Larex\Linters;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\Utils;

class ValidHtmlValueLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return 'Checking valid html values...';
    }

    /**
     * @inheritDoc
     */
    public function handle(Collection $rows): void
    {
        $header = $rows->first();

        $errors = collect([]);

        $rows->skip(1)->each(function ($columns, $line) use ($header, $errors) {
            [$group, $key] = $columns;

            collect($columns)->skip(2)->each(function($value, $column) use ($header, $key, $group, $line, $errors) {
                if(!Utils::isValidHTML($value)){
                    $line++;
                    $lang = $header[$column];
                    $column++;
                    $errors->push("line {$line} ({$group}.{$key}), column: {$column} ({$lang})");
                }
            });
        });

        if ($errors->isNotEmpty()) {
            $subject = Str::plural('string', $errors->count());
            throw new LintException("{$errors->count()} invalid html {$subject} found:", $errors->toArray());
        }
    }
}
