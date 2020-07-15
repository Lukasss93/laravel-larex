<center>
<img src="https://i.imgur.com/CddZo0R.png"/>
</center>

# Laravel Larex
[![Latest Stable Version](https://poser.pugx.org/lukasss93/laravel-larex/v/stable)](https://packagist.org/packages/lukasss93/laravel-larex)
[![Total Downloads](https://poser.pugx.org/lukasss93/laravel-larex/downloads)](https://packagist.org/packages/lukasss93/laravel-larex)
[![License](https://poser.pugx.org/lukasss93/laravel-larex/license)](https://packagist.org/packages/lukasss93/laravel-larex)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Lukasss93/laravel-larex/run-tests)
[![Coverage Status](https://coveralls.io/repos/github/Lukasss93/laravel-larex/badge.svg?branch=master)](https://coveralls.io/github/Lukasss93/laravel-larex?branch=master)

Translate your Laravel application from a single CSV file!

## Installation
You can install the package using composer

```bash
composer require lukasss93/laravel-larex  
```

Then add the service provider to `config/app.php`.  
This step *can be skipped* if package auto-discovery is enabled.

```php
'providers' => [
    Lukasss93\Larex\LarexServiceProvider::class
];
```

## Usage
1. First, you must create the initial CSV file with `php artisan larex:init`.<br>
   Or you can use `php artisan larex:init --base` to init the CSV file with default Laravel entries.<br>
   The csv file has the following columns:
   * group (basically the file name)
   * key (the array key)
   * en (the language code)
   * other language codes...
   
2. Open the *project-root/resources/lang/localization.csv* file and edit it as you see fit.

3. Finally, you can use `php artisan larex` to translate your entries from the csv file to the laravel php files.

### Tips
* The **key** column inside the CSV file supports the **dot notation** for nested arrays.
* You can watch your CSV file with `php artisan larex --watch`
* You can use `php artisan larex:sort` to sort the CSV file by group and key.
* Be careful when using the `php artisan larex` command! It will overwrite all files named with the group names inside the CSV.
* Be careful when using the **dot notation**! Only the **last** entry will override the value.

### Example
1. Run `php artisan larex:init` command
2. Edit the *project-root/resources/lang/localization.csv* file

   | group | key | en | it |
   |---|---|---|---|
   | app | hello | Hello | Ciao |
   | app | developers | Developers | Sviluppatori |
   
3. Run `php artisan larex` command
4. You'll get the following files:
   ```php
   //project-root/resources/lang/en/app.php
   
   <?php
   
   return [
       'hello' => 'Hello',
       'developers' => 'Developers',
   ];
   
   //project-root/resources/lang/it/app.php
   
   <?php
   
   return [
       'hello' => 'Ciao',
       'developers' => 'Sviluppatori',
   ];
   ```

## Testing
```bash
composer test
```

## Changelog
Please see the [CHANGELOG.md](https://github.com/Lukasss93/laravel-larex/blob/master/CHANGELOG.md) for more information on what has changed recently.

## License
Please see the [LICENSE.md](https://github.com/Lukasss93/laravel-larex/blob/master/LICENSE.md) file for more information.
