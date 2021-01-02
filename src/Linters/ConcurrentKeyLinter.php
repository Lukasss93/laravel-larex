<?php

namespace Lukasss93\Larex\Linters;

use Illuminate\Support\Collection;

class ConcurrentKeyLinter implements Linter
{
    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return 'Checking concurrent keys...';
    }
    
    /**
     * @inheritDoc
     */
    public function handle(Collection $rows): void
    {
        //get positions + children
        $count = [];
        $rows->skip(1)->each(function ($columns, $rowN) use (&$count) {
            [$group, $key] = $columns;
            
            if (!array_key_exists($group, $count)) {
                $count[$group] = [];
            }
            
            $this->setKeyPositions($key, $key, $rowN, $count[$group]);
        });
        
        //get errors
        $errors=[];
        foreach ($count as $group=>$keys){
            foreach ($keys as $key=>$item){
                $s=0;
            }
        }
        
        $s = 0;
        /*
        $duplicates = collect($count)->filter(function ($items) {
            return count($items) > 1;
        });

        if ($duplicates->isNotEmpty()) {
            $message = '';
            foreach ($duplicates as $duplicate => $positions) {
                foreach ($positions as $p => $position) {
                    $message .= $position;

                    if ($p < count($positions) - 1) {
                        $message .= ', ';
                    }
                }
                $message .= " ({$duplicate}); ";
            }

            throw new LintException('Concurrent keys found:',[
                'rows 1 (x), 2 (x.y)',
                'rows 3 (a), 4 (a.b)',
            ]);
        }*/
    }
    
    private function setKeyPositions($originalKey, $currentKey, $n, &$array): void
    {
        $keys = collect(explode('.', $currentKey));
        
        $firstKey = $keys->first();
        if (!array_key_exists($firstKey, $array)) {
            $array[$firstKey] = [
                'key' => $currentKey === $firstKey ? $originalKey : null,
                'row' => $currentKey === $firstKey ? $n + 1 : null,
                'children' => [],
            ];
        } else if (array_key_exists($originalKey, $array)) {
            $array[$firstKey]['key'] = $originalKey;
            $array[$firstKey]['row'] = $n + 1;
        }
        
        $otherKeys = $keys->skip(1);
        if ($otherKeys->isNotEmpty()) {
            $this->setKeyPositions($originalKey, $otherKeys->implode('.'), $n, $array[$firstKey]['children']);
        }
    }
}