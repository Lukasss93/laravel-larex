<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Default CSV settings
     |--------------------------------------------------------------------------
     |
     | Here you can specify the default settings for CSV files.
     |
     */

    'csv' => [
        'path' => env('LAREX_CSV_PATH', lang_path('localization.csv')),
    ],

    /*
     |--------------------------------------------------------------------------
     | Larex Exporters
     |--------------------------------------------------------------------------
     |
     | Here you can specify the exporters that will be used by larex:export command.
     | You can add your own exporters (they must implement the \Lukasss93\Larex\Contracts\Exporter interface)
     | by adding them to the "list" array.
     | Calling the "larex:export" command without the exporter parameter will use the "default" exporter.
     |
     */

    'exporters' => [
        'default' => env('LAREX_DEFAULT_EXPORTER', 'laravel'),
        'list' => [
            'laravel' => Lukasss93\Larex\Exporters\LaravelExporter::class,
            'json:lang' => Lukasss93\Larex\Exporters\JsonLanguagesExporter::class,
            'json:group' => Lukasss93\Larex\Exporters\JsonGroupsExporter::class,
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Larex Importers
     |--------------------------------------------------------------------------
     |
     | Here you can specify the importers that will be used by "larex:import" command.
     | You can add your own importers (they must implement the \Lukasss93\Larex\Contracts\Importer interface)
     | by adding them to the "list" array.
     | Calling the "larex:import" command without the importer parameter will use the "default" importer.
     |
     */

    'importers' => [
        'default' => env('LAREX_DEFAULT_IMPORTER', 'laravel'),
        'list' => [
            'laravel' => Lukasss93\Larex\Importers\LaravelImporter::class,
            'json:lang' => Lukasss93\Larex\Importers\JsonLanguagesImporter::class,
            'json:group' => Lukasss93\Larex\Importers\JsonGroupsImporter::class,
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Linters
     |--------------------------------------------------------------------------
     |
     | Linters to run with "larex:lint" command.
     | Linters are executed in the order they are defined.
     | You can disable a linter by commenting it out.
     | You can add your own linters (they must implement the \Lukasss93\Larex\Contracts\Linter interface)
     | by adding them to the list.
     |
     */

    'linters' => [
        Lukasss93\Larex\Linters\ValidHeaderLinter::class,
        Lukasss93\Larex\Linters\ValidLanguageCodeLinter::class,
        Lukasss93\Larex\Linters\DuplicateKeyLinter::class,
        Lukasss93\Larex\Linters\ConcurrentKeyLinter::class,
        Lukasss93\Larex\Linters\NoValueLinter::class,
        Lukasss93\Larex\Linters\DuplicateValueLinter::class,
        // Lukasss93\Larex\Linters\UntranslatedStringsLinter::class,
        // Lukasss93\Larex\Linters\UnusedStringsLinter::class,
        // Lukasss93\Larex\Linters\ValidHtmlValueLinter::class,
        // Lukasss93\Larex\Linters\SameParametersLinter::class,
    ],

    /*
     |--------------------------------------------------------------------------
     | Ignore Empty Values
     |--------------------------------------------------------------------------
     |
     | If true, empty values will be ignored when running the "SameParametersLinter" linter.
     |
     */

    'ignore_empty_values' => env('LAREX_IGNORE_EMPTY_VALUES', false),

    /*
     |--------------------------------------------------------------------------
     | Search Criteria
     |--------------------------------------------------------------------------
     |
     | Here you can specify the search criteria that will be used by:
     | - the "larex:localize" command
     | - the "UntranslatedStringsLinter" linter
     | - the "UnusedStringsLinter" linter
     |
     | The "dirs" array contains the directories to search for strings.
     | Note: it's recursive.
     |
     | The "patterns" array contains the patterns to search for strings.
     | The values can be a regular expression, glob, or just a string.
     |
     | The "functions" array contains the functions that the strings will be extracted from.
     | Note: The translation string should always be the first argument.
     |
     */

    'search' => [
        'dirs' => ['resources/views'],
        'patterns' => ['*.php'],
        'functions' => ['__', 'trans', '@lang'],
    ],

    /*
     |--------------------------------------------------------------------------
     | EOL
     |--------------------------------------------------------------------------
     |
     | End of line character used by "larex:export" command.
     | EOL can be one of the following values:
     | - PHP_EOL (default, based on the operating system)
     | - "\r\n" (Windows)
     | - "\n" (Unix, Mac OS X)
     | - "\r" (Mac OS 9 and before)
     |
     */

    'eol' => PHP_EOL,

    /*
     |--------------------------------------------------------------------------
     | Source Language
     |--------------------------------------------------------------------------
     |
     | The source language is the language used as the base for the translation.
     | This value is used by the "larex:import" command to set the first language column in the CSV file.
     |
     */

    'source_language' => env('LAREX_SOURCE_LANGUAGE', 'en'),
];
