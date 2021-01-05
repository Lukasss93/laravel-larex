<?php

return [
    
    /*
     |--------------------------------------------------------------------------
     | Larex Settings
     |--------------------------------------------------------------------------
     */
    
    /**
     * Default CSV settings
     */
    'csv' => [
        'path' => 'resources/lang/localization.csv',
        'delimiter' => ';',
        'enclosure' => '"',
        'escape' => '\\',
    ],
    
    /**
     * Linters to run with larex:lint command
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
    ],
    
    /**
     * Search criteria for file used by:
     * - UntranslatedStringsLinter
     * - UnusedStringsLinter
     */
    'search' => [
        /**
         * Directories which should be looked inside.
         * NOTE: It's recursive.
         */
        'dirs' => ['resources/views'],
        
        /**
         * Patterns by which files should be queried.
         * The values can be a regular expression, glob, or just a string.
         */
        'patterns' => ['*.php'],
        
        /**
         * Functions that the strings will be extracted from.
         * Add here any custom defined functions.
         * NOTE: The translation string should always be the first argument.
         */
        'functions' => ['__', 'trans', '@lang']
    ],

];
