# Upgrading

Because there are many breaking changes an upgrade is not that easy. There are many edge cases this guide does not
cover. We accept PRs to improve this guide.

## From v4.2 to v4.3.0

- The `larex.php` config was changed:
    - **Optional**. Please append new `source_language` key in this way:
      ```php
      'source_language' => 'en',
      ```

_Or you can delete your current config and publish it again (copy your changes before)._

## From v4.0 to v4.2

- The `larex.php` config was changed:
    - **Optional**. Please append new `SameParametersLinter` linter in this way:
      ```php
      'linters' => [
          // other linters here
          // Lukasss93\Larex\Linters\SameParametersLinter::class,
      ],
      ```
  - **Optional**. Please append `ignore_empty_values` key in this way:
    ```php
    'ignore_empty_values' => false,
    ```
_Or you can delete your current config and publish it again (copy your changes before)._

## From v3.x to v4.0
- The `larex.php` config was changed. Please change the `path` key in this way:
```php
// <project-root>/config/larex.php

// before
'path' => 'resources/lang/localization.csv',

// after (Laravel 9)
'path' => lang_path('localization.csv'),

// after (Laravel 8)
'path' => resource_path('lang/localization.csv'),
```

_Or you can delete your current config and publish it again (copy your changes before)._
- ⚠️ Dropped **PHP 7.4** support, please upgrade at least to **PHP 8.0**.
- ⚠️ Dropped **Laravel 7** support, please upgrade at least to **Laravel 8**.


## From v2.1 to v3.x

- The `larex.php` config was changed. Please delete your current config and publish it again (copy your changes before).
- If you created a custom Linter, please follow this steps:
    - Change `public function description()` to `public static function description()`
    - Change `public function handle(Collection $row)` to `public function handle(CsvReader $reader)`
        - The CsvReader class exposes the following methods:
            - `getHeader` to get the CSV header as `Collection`
            - `getRows` to get the CSV rows as `LazyCollection`

- ⚠️ Dropped **PHP 7.3** support, please upgrade at least to **PHP 7.4**.
- ⚠️ Dropped **Laravel 6** support, please upgrade at least to **Laravel 7**.

## From v1.6 to v2.0

Because the CSV default format was changed, please follow this steps:

1. ⚠️ Please run the `php artisan larex:export` **before upgrading to v2.0!**
2. Delete the `localization.csv` file inside _project-root/resources/lang_ folder
3. Upgrade the library to **v2.0**
4. Run the `php artisan larex:import` command

## From v1.5 to v1.6

- No breaking changes.
- ⚠️ Dropped support for **PHP 7.2**, please upgrade at least to **PHP 7.3**.
