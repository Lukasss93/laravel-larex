<?php

namespace Lukasss93\Larex\Contracts;

use Illuminate\Support\Collection;
use Lukasss93\Larex\Console\LarexImportCommand;

interface Importer
{
    /**
     * Exporter description.
     * @return string
     */
    public static function description(): string;

    /**
     * Exporter logic.
     * @param LarexImportCommand $command
     * @return Collection
     */
    public function handle(LarexImportCommand $command): Collection;
}
