<?php

namespace Lukasss93\Larex\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader;

class CsvReader
{
    protected SimpleExcelReader $reader;

    protected function __construct(string $path)
    {
        $this->reader = SimpleExcelReader::create($path);
    }

    public static function create(string $path): self
    {
        return new self($path);
    }

    public function getHeader(): Collection
    {
        $header = $this->reader->getHeaders();

        return is_array($header) ? collect($header) : collect([]);
    }

    public function getRows(): LazyCollection
    {
        return $this->reader->getRows()->mapInto(Collection::class);
    }

    public function getReader(): SimpleExcelReader
    {
        return $this->reader;
    }

    public function getPath(): string
    {
        return $this->reader->getPath();
    }
}
