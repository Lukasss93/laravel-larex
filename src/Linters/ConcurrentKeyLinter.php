<?php

namespace Lukasss93\Larex\Linters;

use Lukasss93\Larex\Contracts\Linter;
use Lukasss93\Larex\Exceptions\LintException;
use Lukasss93\Larex\Support\CsvReader;

class ConcurrentKeyLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Checking concurrent keys...';
    }

    /**
     * @inheritDoc
     */
    public function handle(CsvReader $reader): void
    {
        //get keys position
        $count = [];
        $reader->getRows()->each(function ($columns, $line) use (&$count) {
            $line+=2;

            if (!array_key_exists($columns['group'], $count)) {
                $count[$columns['group']] = [];
            }

            $this->setKeyPositions($columns['key'], $columns['key'], $line, $count[$columns['group']]);
        });

        //get raw errors
        $rawErrors = [];
        collect($count)->each(function ($keys, $group) use (&$rawErrors) {
            $this->parseErrors($group, $keys, $rawErrors);
        });

        //build errors
        $errors = [];
        collect($rawErrors)->each(function ($keys, $group) use (&$errors) {
            foreach ($keys as $key) {
                $error = [];
                $this->buildErrors($key, $error);
                $errors[$group][] = $error;
            }

        });

        //print errors
        $messages = [];
        foreach ($errors as $group => $keys) {
            foreach ($keys as $subkeys) {
                $text = 'rows ';
                $errorKeys = collect($subkeys)->sort();
                $i = 0;
                foreach ($errorKeys as $key => $row) {
                    $text .= "{$row} ({$group}.{$key})";

                    if ($i < $errorKeys->count() - 1) {
                        $text .= ', ';
                    }
                    $i++;
                }
                $text .= ';';
                $messages[] = $text;
            }
        }

        if (count($messages) > 0) {
            throw new LintException('Concurrent keys found:', $messages);
        }
    }

    private function setKeyPositions($originalKey, $currentKey, $n, &$array): void
    {
        $keys = collect(explode('.', $currentKey));

        $firstKey = $keys->first();
        if (!array_key_exists($firstKey, $array)) {
            $key = $currentKey === $firstKey ? $originalKey : null;
            $row = $currentKey === $firstKey ? $n : null;
            $array[$firstKey] = ['key' => $key, 'row' => $row, 'children' => []];
        } else if (array_key_exists($originalKey, $array)) {
            $array[$firstKey]['key'] = $originalKey;
            $array[$firstKey]['row'] = $n;
        }

        $otherKeys = $keys->skip(1);
        if ($otherKeys->isNotEmpty()) {
            $this->setKeyPositions($originalKey, $otherKeys->implode('.'), $n, $array[$firstKey]['children']);
        }
    }

    private function parseErrors(string $group, array $keys, &$errors): void
    {
        foreach ($keys as $key) {

            if (count($key['children']) === 0) {
                continue;
            }

            if ($key['row'] !== null) {
                $errors[$group][] = $key;
                continue;
            }

            if ($key['row'] === null) {
                $this->parseErrors($group, $key['children'], $errors);
            }
        }
    }

    private function buildErrors(array $key, &$error): void
    {
        if ($key['row'] !== null) {
            $error[$key['key']] = $key['row'];
        }

        foreach ($key['children'] as $child) {
            $this->buildErrors($child, $error);
        }
    }
}
