<?php


namespace Lukasss93\Larex\Linters;


use Illuminate\Support\Str;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;

class DuplicateKeyLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Checking duplicated keys...';
    }

    /**
     * @inheritDoc
     */
    public function handle(CsvReader $reader): void
    {
        $count = [];

        $reader->getRows()->each(function ($columns, $line) use (&$count) {
            $line+=2;
            $count["{$columns['group']}.{$columns['key']}"][] = $line;
        });

        $duplicates = collect($count)->filter(function ($items) {
            return count($items) > 1;
        });

        if ($duplicates->isNotEmpty()) {
            $errors = collect([]);

            foreach ($duplicates as $duplicate => $positions) {
                $message = '';
                foreach ($positions as $p => $position) {
                    $message .= $position;

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
