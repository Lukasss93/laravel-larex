<?php

function csv_path($relative = false): string
{
    $path = config('larex.csv.path');

    if ($relative) {
        return ltrim(str_replace(base_path(), '', $path), '/\\');
    }

    return $path;
}
