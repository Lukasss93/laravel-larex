<?php

namespace Lukasss93\Larex\Contracts;

use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;

interface Linter
{
    /**
     * Linter description.
     * @return string
     */
    public static function description(): string;

    /**
     * Linter logic.
     * @param CsvReader $reader
     * @throws LintException
     */
    public function handle(CsvReader $reader): void;
}
