<?php

namespace Lukasss93\Larex\Linters;

use Illuminate\Support\Collection;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;

class SameParametersLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Checking same parameters...';
    }

    /**
     * @inheritDoc
     */
    public function handle(CsvReader $reader): void
    {
        $errors = collect([]);

        //get headers
        $headers = $reader->getHeader()->flip();

        //check if all parameters are the same
        $reader->getRows()->each(function (Collection $columns, int $line) use ($headers, &$errors) {
            $line += 2;

            $group = $columns->get('group');
            $key = $columns->get('key');

            //get parameters for every language
            $parameters = $columns
                ->skip(2)
                ->map(function (string $item) {
                    preg_match_all('/:\w+/', $item, $matches);

                    return collect($matches[0] ?? []);
                });

            //get first item with max parameters count
            /** @var Collection $max */
            $max = $parameters->sortByDesc(fn ($item) => count($item))->first();

            //check if all parameters are the same
            foreach ($parameters as $lang => $items) {
                $column = $headers->get($lang) + 1;
                $value = $columns->get($lang);

                if (blank($value) && config('larex.ignore_empty_values', false)) {
                    continue;
                }

                foreach ($max->diff($items) as $param) {
                    $errors->push("line $line ($group.$key), column $column ($lang): missing $param parameter");
                }
            }
        });

        if ($errors->isNotEmpty()) {
            throw new LintException('Missing parameters found:', $errors->toArray());
        }
    }
}
