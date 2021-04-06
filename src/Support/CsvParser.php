<?php


namespace Lukasss93\Larex\Support;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CsvParser
{
    /** @var Collection $rows */
    private $rows;

    /** @var string[] $warning */
    private $warning;

    /** @var bool $handleSubKeys */
    private $handleSubKeys;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
        $this->warning = [];
        $this->handleSubKeys = true;
    }

    public static function create(Collection $rows): self
    {
        return new self($rows);
    }

    public function setHandleSubKey(bool $value): self
    {
        $this->handleSubKeys = $value;
        return $this;
    }

    public function getWarnings(): array
    {
        return $this->warning;
    }

    public function parse(): array
    {
        $languages = [];
        $header = $this->rows->get(0);
        $columnsCount = $header->count();
        $lines = $this->rows->skip(1);

        //loop rows
        foreach ($lines as $i => $columns) {
            $line = $i + 1;

            //check if row is blank
            if ($columns->count() <= 1 && $columns->get(0) === null) {
                $this->warning[] = sprintf('Invalid row at line %d. The row will be skipped.', $line);
                continue;
            }

            //get first two columns values
            [$group, $key] = $columns;

            //check if key is filled
            if ($key === '') {
                $this->warning[] = sprintf('Missing key name at line %d. The row will be skipped.', $line);
                continue;
            }

            //loop language columns
            for ($j = 2; $j < $columnsCount; $j++) {
                $item = $columns->get($j) ?? '';
                $column = $j + 1;

                if ($item === '') {
                    $this->warning[] = sprintf('%s.%s at line %d, column %d (%s) is missing. It will be skipped.', $group, $key, $line, $column, $header[$j]);
                    continue;
                }

                if ($this->handleSubKeys) {
                    Arr::set($languages[$header[$j]][$group], $key, $item);
                } else {
                    $languages[$header[$j]][$group][$key] = $item;
                }
            }
        }

        //sort languages by column order
        $languages = collect($languages)
            ->sortBy(function ($item, $key) use ($header) {
                return array_search($key, $header->skip(2)->toArray(), true);
            })
            ->toArray();

        return $languages;
    }

    public function validate(): bool
    {
        //TODO: group, key, langs


    }
}
