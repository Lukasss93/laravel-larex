<?php

function csv_path($relative = false): string
{
    $path = config('larex.csv.path');

    if ($relative) {
        $path = str_replace(base_path(), '', $path);
        $path = ltrim($path, '/\\');

        return $path;
    }

    return $path;
}
