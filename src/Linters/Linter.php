<?php

namespace Lukasss93\Larex\Linters;

use Illuminate\Support\Collection;
use Lukasss93\Larex\Exceptions\LintException;

interface Linter
{
    /**
     * Linter description
     * @return string
     */
    public function description(): string;

    /**
     * Linter logic
     * @param Collection $rows
     * @throws LintException
     */
    public function handle(Collection $rows): void;
}