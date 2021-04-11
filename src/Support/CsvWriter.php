<?php

namespace Lukasss93\Larex\Support;

use InvalidArgumentException;
use RuntimeException;

class CsvWriter
{
    /** @var string */
    private $path;

    /** @var resource */
    private $filePointer;

    /** @var string */
    private $delimiter;

    /** @var string */
    private $enclosure;

    /** @var string */
    private $escape;

    /** @var bool */
    private $processHeader = true;

    /** @var bool */
    private $processingFirstRow = true;

    /** @var int */
    private $numberOfRows = 0;

    protected function __construct(string $path)
    {
        $this->path = $path;
        $this->delimiter = ',';
        $this->enclosure = '"';
        $this->escape = '"';
        $this->filePointer = fopen($this->path, 'wb+');
    }

    public static function create(string $path): self
    {
        return new self($path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getNumberOfRows(): int
    {
        return $this->numberOfRows;
    }

    public function noHeaderRow(): self
    {
        $this->processHeader = false;

        return $this;
    }

    public function useDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    public function useEnclosure(string $enclosure): self
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function useEscape(string $escape): self
    {
        $this->escape = $escape;

        return $this;
    }

    public function addRow(array $row): self
    {
        if ($this->processHeader && $this->processingFirstRow) {
            $this->writeHeaderFromRow($row);
        }

        $wasWriteSuccessful = Utils::fputcsv($this->filePointer, array_values($row), $this->delimiter, $this->enclosure, $this->escape);
        if ($wasWriteSuccessful === false) {
            $this->closeAndClean();
            throw new RuntimeException('Unable to write data');
        }

        $this->numberOfRows++;

        $this->processingFirstRow = false;

        return $this;
    }

    public function addRows(array $rows): self
    {
        foreach ($rows as $row) {
            if (!is_array($row)) {
                $this->closeAndClean();
                throw new InvalidArgumentException('The input should be an array of array');
            }

            $this->addRow($row);
        }

        return $this;
    }

    public function whenAddRow(bool $condition, array $rowTrue, ?array $rowFalse = null): self
    {
        if ($condition) {
            $this->addRow($rowTrue);
        } elseif ($rowFalse !== null && is_array($rowFalse)) {
            $this->addRow($rowFalse);
        }

        return $this;
    }

    protected function writeHeaderFromRow(array $row): void
    {
        $headerValues = array_keys($row);

        $wasWriteSuccessful = Utils::fputcsv($this->filePointer, $headerValues, $this->delimiter, $this->enclosure, $this->escape);
        if ($wasWriteSuccessful === false) {
            $this->closeAndClean();
            throw new RuntimeException('Unable to write data');
        }

        $this->numberOfRows++;
    }

    protected function close(): void
    {
        if (is_resource($this->filePointer)) {
            fclose($this->filePointer);
        }
    }

    protected function closeAndClean(): void
    {
        $this->close();

        if (file_exists($this->path) && is_file($this->path)) {
            unlink($this->path);
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}
