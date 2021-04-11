<?php

namespace Lukasss93\Larex\Contracts;

use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Support\CsvReader;

interface Exporter
{
    /**
     * Exporter description.
     * @return string
     */
    public static function description(): string;

    /**
     * Exporter logic.
     * @param LarexExportCommand $command
     * @param CsvReader $reader
     * @return int
     */
    public function handle(LarexExportCommand $command, CsvReader $reader): int;
}
