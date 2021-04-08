<?php


namespace Lukasss93\Larex\Linters;


use Illuminate\Support\Collection;
use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;

class ValidHeaderLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return 'Validating header structure...';
    }

    /**
     * @inheritDoc
     */
    public function handle(CsvReader $reader): void
    {
        $header=$reader->getHeader();

        if(!isset($header[0])){
            throw new LintException('First header column is missing.');
        }

        if($header[0]!=='group'){
            throw new LintException('First header column value is invalid. Must be "group".');
        }

        if(!isset($header[1])){
            throw new LintException('Second header column is missing.');
        }

        if($header[1]!=='key'){
            throw new LintException('Second header column value is invalid. Must be "key".');
        }

        if(count($header)<=2){
            throw new LintException('No language columns found.');
        }
    }
}
