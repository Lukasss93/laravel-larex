<?php

namespace Lukasss93\Larex\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CsvParser
{
    protected CsvReader $reader;

    /** @var string[] */
    protected array $warning;

    protected bool $handleSubKeys;

    public function __construct(CsvReader $reader)
    {
        $this->reader = $reader;
        $this->warning = [];
        $this->handleSubKeys = true;
    }

    public static function create(CsvReader $reader): self
    {
        return new self($reader);
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

    public function parse(bool $skipEmpty = true): array
    {
        $languages = [];
        $header = $this->reader->getHeader();
        $rows = $this->reader->getRows()->collect();

        $this->validateRaw();

        //loop rows
        foreach ($rows as $columns) {

            //get first two columns values
            $group = trim($columns->get('group'));
            $key = trim($columns->get('key'));

            //check if key is filled
            if ($key === '') {
                continue;
            }

            //loop language columns
            foreach ($columns->skip(2) as $lang => $value) {
                $lang = trim($lang);
                $item = $value ?? '';

                if ($item === '') {
                    if ($skipEmpty) {
                        continue;
                    }

                    if (!array_key_exists($lang, $languages)) {
                        $languages[$lang] = [];
                    }

                    continue;
                }

                if ($this->handleSubKeys) {
                    Arr::set($languages[$lang][$group], $key, $item);
                } else {
                    $languages[$lang][$group][$key] = $item;
                }
            }
        }

        //sort languages by column order
        return collect($languages)
            ->sortBy(fn ($item, $key) => array_search($key, $header->skip(2)->toArray(), true))
            ->toArray();
    }

    public function validateRaw(): void
    {
        //read raw csv
        /** @var Collection<int,Collection<int,string>> $output */
        $output = collect([]);
        $file = fopen($this->reader->getPath(), 'rb');
        while (($cols = fgetcsv($file)) !== false) {
            /** @var Collection<int,string> $columns */
            $columns = collect($cols ?? []);
            $output->push($columns);
        }
        fclose($file);

        //loop collection
        foreach ($output->skip(1) as $i => $columns) {
            $line = $i + 1;

            //check if row is blank
            if ($columns->count() <= 1 && $columns->get(0) === null) {
                $this->warning[] = sprintf('Invalid row at line %d. The row will be skipped.', $line);
                continue;
            }

            //get first two columns values
            $group = $columns->get(0);
            $key = $columns->get(1);

            //check if key is filled
            if ($key === '') {
                $this->warning[] = sprintf('Missing key name at line %d. The row will be skipped.', $line);
                continue;
            }

            //loop language columns
            foreach ($columns->skip(2) as $j => $value) {
                $item = $value ?? '';
                $column = $j + 1;

                if ($item === '') {
                    $this->warning[] = sprintf(
                        '%s.%s at line %d, column %d (%s) is missing. It will be skipped.',
                        $group, $key, $line, $column, $output->first()->get($j)
                    );
                }
            }
        }
    }
}
