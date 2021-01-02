<?php

namespace Lukasss93\Larex\Linters;

use Illuminate\Support\Collection;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Utils;

class ValidLanguageCodeLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return 'Validating language codes in header columns...';
    }

    /**
     * @inheritDoc
     */
    public function handle(Collection $rows): void
    {
        collect($rows->first())->skip(2)->each(function ($code, $n) {
            $suggest = Utils::isValidLanguageCode($code);

            if ($suggest !== true) {
                $column = $n + 1;
                $suggest = is_string($suggest) ? " Did you mean: {$suggest}" : '';
                throw new LintException("Language code not valid in column {$column} ({$code})." . $suggest);
            }
        });
    }
}