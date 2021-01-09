# Upgrading
Because there are many breaking changes an upgrade is not that easy. 
There are many edge cases this guide does not cover. 
We accept PRs to improve this guide.

## From v1.6 to v2.0
Because the CSV default format was changed, please follow this steps:
1. Please run the `php artisan larex:export` **before upgrading to v2.0!**
2. Delete the `localization.csv` file inside _project-root/resources/lang_ folder
2. Upgrade the library to **v2.0**
3. Run the `php artisan larex:import` command

## From v1.5 to v1.6
- No breaking changes
- Dropped support for **PHP 7.2**, please update at least to **PHP 7.3**
