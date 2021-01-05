<p align="center">
    <img src="https://i.imgur.com/GrpbNU4.png"/>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/php-%3E%3D%207.3-blue"/>
  <img src="https://img.shields.io/badge/laravel-%3E%3D%206.0-orange"/>
  <a href="https://packagist.org/packages/lukasss93/laravel-larex">
    <img src="https://poser.pugx.org/lukasss93/laravel-larex/v/stable"/>
  </a>
  <a href="https://packagist.org/packages/lukasss93/laravel-larex">
    <img src="https://poser.pugx.org/lukasss93/laravel-larex/downloads"/>
  </a>
  <a href="https://packagist.org/packages/lukasss93/laravel-larex">
    <img src="https://poser.pugx.org/lukasss93/laravel-larex/license"/>
  </a>
  <a href="https://t.me/Lukasss93">
    <img src="https://img.shields.io/badge/chat%20on-telegram-blue"/>
  </a>
  <img src="https://img.shields.io/github/workflow/status/Lukasss93/laravel-larex/run-tests"/>
  <a href="https://codecov.io/gh/Lukasss93/laravel-larex">
    <img src="https://img.shields.io/codecov/c/github/lukasss93/laravel-larex?token=XcLU2ccFQ7"/>
  </a>
</p>

<p align="center">
    Translate your Laravel application from a single CSV file!
</p>

## 🚀 Installation

You can install the package using composer

```bash
composer require lukasss93/laravel-larex --dev
```

Then add the service provider to `config/app.php`.  
This step *can be skipped* if package auto-discovery is enabled.

```php
'providers' => [
    Lukasss93\Larex\LarexServiceProvider::class
];
```

## ⚙ Publishing the config file

Publishing the config file is optional:

```bash
php artisan vendor:publish --provider="Lukasss93\Larex\LarexServiceProvider" --tag="larex-config"
```

## 👓 Usage

1. First, you must create the initial CSV file with `php artisan larex:init`.<br>
   Or you can use `php artisan larex:import` to import entries from resources/lang files.<br>
   The csv file has the following columns:
   
   * group (basically the file name)
   * key (the array key)
   * en (the language code)
   * other language codes...

2. Open the *project-root/resources/lang/localization.csv* file and edit it as you see fit.

3. Finally, you can use `php artisan larex:export` to translate your entries from the csv file to the laravel php files.

### ✨ Tips

* You can import existing laravel php files with `php artisan larex:import`.
* You can use `php artisan larex:init --base` to init the CSV file with default Laravel entries.
* The **key** column inside the CSV file supports the **dot notation** for nested arrays.
* You can watch your CSV file with `php artisan larex:export --watch`
* You can use `php artisan larex:sort` to sort the CSV file by group and key.
* Be careful when using the `php artisan larex:export` command! It will overwrite all files named with the group names inside the CSV.
* Be careful when using the **dot notation**! Only the **last** entry will override the value.
* You can use `php artisan larex:insert` to add new items via CLI too!
* You can use `php artisan larex:export --include=en,it` to export only _"en"_ and _"it"_ columns.
* You can use `php artisan larex:export --exclude=it` to export all columns except _"it"_ column.

### 📝 Example

1. Run `php artisan larex:init` command

2. Edit the *project-root/resources/lang/localization.csv* file
   
   | group | key             | en         | it           |
   | ----- | --------------- | ---------- | ------------ |
   | app   | hello           | Hello      | Ciao         |
   | app   | list.developers | Developers | Sviluppatori |
   | app   | list.teachers   | Teachers   | Insegnanti   |

3. Run `php artisan larex:export` command

4. You'll get the following files:
   
   ```php
   //project-root/resources/lang/en/app.php
   
   <?php
   
   return [
       'hello' => 'Hello',
       'list' => [
           'developers' => 'Developers',
           'teachers' => 'Teachers',
       ]
   ];
   ```
   
   ```php
   //project-root/resources/lang/it/app.php
   
   <?php
   
   return [
       'hello' => 'Ciao',
       'list' => [
           'developers' => 'Sviluppatori',
           'teachers' => 'Insegnanti',
       ]
   ];
   ```

### 🔍 Linters

Larex provides a linting system by using the `php artisan larex:lint` command 
to validate your CSV file.

##### Available linters:

| Linter                    | Enabled by default | Description                                       |
| ------------------------- | ------------------ | ------------------------------------------------- |
| ValidHeaderLinter         | Yes                | Validate the header structure                     |
| ValidLanguageCodeLinter   | Yes                | Validate the language codes in the header columns |
| DuplicateKeyLinter        | Yes                | Find duplicated keys                              |
| ConcurrentKeyLinter       | Yes                | Find concurrent keys                              |
| NoValueLinter             | Yes                | Find missing values                               |
| DuplicateValueLinter      | Yes                | Find duplicated values in the same row            |
| UntranslatedStringsLinter | No                 | Find untranslated strings                         |
| UntranslatedStringsLinter | No                 | Find unused strings                               |
| ValidHtmlValueLinter      | No                 | Check valid html values                           |

You can enable/disable any linter you want by comment/uncomment it inside the larex config.

##### How to create a linter:
1. Create a class that implements the `Lukasss93\Larex\Linters\Linter` interface 
2. Add your linter inside the larex config

## ⚗️ Testing

```bash
composer test
```

## 📃 Changelog

Please see the [CHANGELOG.md](https://github.com/Lukasss93/laravel-larex/blob/master/CHANGELOG.md) for more information on what has changed recently.

## 📖 License

Please see the [LICENSE.md](https://github.com/Lukasss93/laravel-larex/blob/master/LICENSE.md) file for more information.
