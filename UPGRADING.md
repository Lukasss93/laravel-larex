# Upgrading

Because there are many breaking changes an upgrade is not that easy. There are many edge cases this guide does not
cover. We accept PRs to improve this guide.

## From v2.1 to v3.0

- The `larex.php` config was changed. Please delete your current config and publish it again (copy your changes before)
- If you created a custom Linter, please follow this steps:
    - Change `public function description()` to `public static function description()`
    - Change `public function handle(Collection $row)` to `public function handle(CsvReader $reader)`
        - The CsvReader class exposes the following methods:
            - `getHeader` to get the CSV header as `Collection`
            - `getRows` to get the CSV rows as `LazyCollection`

- Dropped PHP 7.3 support, please upgrade at least to PHP 7.4
- Dropped Laravel 6 support, please upgrade at least to Laravel 7

## From v1.6 to v2.0

Because the CSV default format was changed, please follow this steps:

1. Please run the `php artisan larex:export` **before upgrading to v2.0!**
2. Delete the `localization.csv` file inside _project-root/resources/lang_ folder
3. Upgrade the library to **v2.0**
4. Run the `php artisan larex:import` command

## From v1.5 to v1.6

- No breaking changes
- Dropped support for **PHP 7.2**, please upgrade at least to **PHP 7.3**
