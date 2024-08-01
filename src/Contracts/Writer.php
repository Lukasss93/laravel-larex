<?php

namespace Lukasss93\Larex\Contracts;

use Illuminate\Support\Collection;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Exceptions\WriterException;

interface Writer
{
    /**
     * Writer description.
     * @return string
     */
    public static function description() : string;

    /**
     * Writer logic.
     * @param Collection $data
     * @return bool
     * @throws WriterException
     */
    public function handle(Collection $data) : self;

}