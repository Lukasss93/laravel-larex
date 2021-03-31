<?php

namespace Lukasss93\Larex\Contracts;

use Illuminate\Support\Collection;
use Lukasss93\Larex\Console\LarexExportCommand;

interface Exporter
{
    /**
     * Exporter description
     * @return string
     */
    public static function description(): string;
    
    /**
     * Exporter logic
     * @param LarexExportCommand $command
     * @param Collection $rows
     * @return int
     */
    public function handle(LarexExportCommand $command, Collection $rows): int;
}