<?php

use Illuminate\Support\Str;

function csv_path($relative = false): string
{
    $path = config('larex.csv.path');

    if ($relative) {
        return ltrim(str_replace(base_path(), '', $path), '/\\');
    }

    return $path;
}

function lang_rpath(string $value): string
{
    $value = ltrim($value, '\\/');

    return Str::of(lang_path($value))
        ->replace(base_path(), '')
        ->replace('lang\\', 'lang/')
        ->ltrim('\\/');
}
