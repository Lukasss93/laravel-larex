<p align="center">
    <img style="max-height:400px" src="https://banners.beyondco.de/Laravel%20Larex.png?theme=dark&packageManager=composer+require&packageName=lukasss93%2Flaravel-larex+--dev&pattern=graphPaper&style=style_1&description=Translate+your+Laravel+application+with+a+single+CSV+file%21+&md=1&showWatermark=0&fontSize=125px&images=table"/>
</p>

# Laravel Larex

[![Version](https://img.shields.io/packagist/v/lukasss93/laravel-larex?label=composer&logo=composer)](https://packagist.org/packages/lukasss93/laravel-larex)
[![Downloads](https://img.shields.io/packagist/dt/lukasss93/laravel-larex)](https://packagist.org/packages/lukasss93/laravel-larex)
![License](https://img.shields.io/packagist/l/lukasss93/laravel-larex)
![PHP](https://img.shields.io/packagist/dependency-v/lukasss93/laravel-larex/php?logo=php)
![Laravel](https://img.shields.io/packagist/dependency-v/lukasss93/laravel-larex/illuminate/support?label=laravel&logo=laravel)

![Tests](https://img.shields.io/github/actions/workflow/status/lukasss93/laravel-larex/run-tests.yml?label=Test%20Suite&logo=github)
[![Maintainability](https://qlty.sh/gh/Lukasss93/projects/laravel-larex/maintainability.svg)](https://qlty.sh/gh/Lukasss93/projects/laravel-larex)
[![Code Coverage](https://qlty.sh/gh/Lukasss93/projects/laravel-larex/coverage.svg)](https://qlty.sh/gh/Lukasss93/projects/laravel-larex)

> Laravel Larex lets you translate your whole Laravel application with a single CSV file.

You can import translation entries from lang folder into a structured CSV, edit the translations and export them back to Laravel PHP files.

Laravel Larex also supports functionalities to sort entries and find strings that aren't localized yet.

_See **[Plugins](#-plugins)** section for other features._

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
   Or you can use `php artisan larex:import` to import entries from lang folder.<br>
   The csv file has the following columns:
   
   * group (basically the file name)
   * key (the array key)
   * en (the language code)
   * other language codes...

2. Open the *project-root/lang/localization.csv* file and edit it as you see fit.

3. Finally, you can use `php artisan larex:export` to translate your entries from the csv file to the laravel php files.

### ✨ Tips

* You can import existing laravel php files with `php artisan larex:import`.
* You can use `php artisan larex:init --base` to init the CSV file with default Laravel entries.
* The **key** column inside the CSV file supports the **dot notation** for nested arrays.
* You can watch your CSV file with `php artisan larex:export --watch`
* You can use `php artisan larex:sort` to sort the CSV file by group and key.
* Be careful when using the `php artisan larex:export` command! It will overwrite all files named with the group names
  inside the CSV.
* Be careful when using the **dot notation**! Only the **last** entry will override the value.
* You can use `php artisan larex:insert` to add new items via CLI too!
* You can use `php artisan larex:import --include=en,it` to import only _"en"_ and _"it"_ items.
* You can use `php artisan larex:import --exclude=it` to import all items except _"it"_ item.
* You can use `php artisan larex:export --include=en,it` to export only _"en"_ and _"it"_ columns.
* You can use `php artisan larex:export --exclude=it` to export all columns except _"it"_ column.
* You can use `php artisan larex:localize` to find unlocalized strings (use the `--import` option to add strings in your
  CSV).
* You can use `php artisan larex:find` to search existing groups or keys in your CSV file.
* You can use `php artisan larex:remove` to remove existing strings in your CSV file.
* You can use `php artisan larex:lang:add` to add a new language column to your CSV file.
* You can use `php artisan larex:lang:remove` to remove a language column from your CSV file.
* You can use `php artisan larex:lang:order` to reorder language columns in your CSV file.

### 📝 Example

1. Run `php artisan larex:init` command

2. Edit the *project-root/lang/localization.csv* file
   
| group | key             | en         | it           |
|-------|-----------------|------------|--------------|
| app   | hello           | Hello      | Ciao         |
| app   | list.developers | Developers | Sviluppatori |
| app   | list.teachers   | Teachers   | Insegnanti   |

3. Run `php artisan larex:export` command

4. You'll get the following files:
   
   ```php
   //project-root/lang/en/app.php
   
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
   //project-root/lang/it/app.php
   
   <?php
   
   return [
       'hello' => 'Ciao',
       'list' => [
           'developers' => 'Sviluppatori',
           'teachers' => 'Insegnanti',
       ]
   ];
   ```

### ⏫ Exporters

The `larex:export <exporter>` command exports CSV strings to a specific location based on the selected exporter.

| Exporter   | Default | Description                                        |
|------------|:-------:|----------------------------------------------------|
| laravel    |    ✅    | Export data from CSV to Laravel localization files |
| json:group |    ❌    | Export data from CSV to JSON by group              |
| json:lang  |    ❌    | Export data from CSV to JSON by language           |

##### How to create an exporter:

1. Create a class that implements the `Lukasss93\Larex\Contracts\Exporter` interface
2. Add your exporter inside the larex config

### ⏬ Importers

The `larex:import <importer>` command imports the strings of the selected importer, into the CSV.

| Importer   |  Default  | Description                                        |
|------------|:---------:|----------------------------------------------------|
| laravel    |     ✅     | Import data from Laravel localization files to CSV |
| json:group |     ❌     | Import data from JSON by group to CSV              |
| json:lang  |     ❌     | Import data from JSON by language to CSV           |

##### How to create an importer:

1. Create a class that implements the `Lukasss93\Larex\Contracts\Importer` interface
2. Add your importer inside the larex config

### 🔍 Linters

Larex provides a linting system by using the `php artisan larex:lint` command to validate your CSV file.

##### Available linters:

| Linter                    | Enabled<br>by default | Description                                       |
|---------------------------|:---------------------:|---------------------------------------------------|
| ValidHeaderLinter         |           ✅           | Validate the header structure                     |
| ValidLanguageCodeLinter   |           ✅           | Validate the language codes in the header columns |
| DuplicateKeyLinter        |           ✅           | Find duplicated keys                              |
| ConcurrentKeyLinter       |           ✅           | Find concurrent keys                              |
| NoValueLinter             |           ✅           | Find missing values                               |
| DuplicateValueLinter      |           ✅           | Find duplicated values in the same row            |
| UntranslatedStringsLinter |           ❌           | Find untranslated strings                         |
| UntranslatedStringsLinter |           ❌           | Find unused strings                               |
| ValidHtmlValueLinter      |           ❌           | Check valid html values                           |
| SameParametersLinter      |           ❌           | Check same parameters in each language            |

You can enable/disable any linter you want by comment/uncomment it inside the larex config.

##### How to create a linter:

1. Create a class that implements the `Lukasss93\Larex\Contracts\Linter` interface
2. Add your linter inside the larex config

## 🧩 Plugins
- **[Crowdin Plugin](https://github.com/Lukasss93/laravel-larex-crowdin)** <br>
  _A Laravel Larex plugin to import/export localization strings from/to Crowdin_

## ⚗️ Testing

```bash
composer test
```

## 🔰 Version Support

|      Larex       | L5.8 | L6.x | L7.x | L8.x | L9.x | L10.x | L11.x | L12.x | L13.x |
|:----------------:|:----:|:----:|:----:|:----:|:----:|:-----:|:-----:|:-----:|:-----:|
|       ^1.0       |  ✅   |  ✅   |  ✅   |  ✅   |  ❌   |   ❌   |   ❌   |   ❌   |   ❌   |
| ^1.2 &#124; ^2.0 |  ❌   |  ✅   |  ✅   |  ✅   |  ❌   |   ❌   |   ❌   |   ❌   |   ❌   |
|       ^3.0       |  ❌   |  ❌   |  ✅   |  ✅   |  ❌   |   ❌   |   ❌   |   ❌   |   ❌   |
|       ^4.0       |  ❌   |  ❌   |  ❌   |  ✅   |  ✅   |   ✅   |   ✅   |   ✅   |   ✅   |

|      Larex       | PHP7.2 | PHP7.3 | PHP7.4 | PHP8.0 | PHP8.1 | PHP8.2 | PHP8.3 | PHP8.4 | PHP8.5 |
|:----------------:|:------:|:------:|:------:|:------:|:------:|:------:|:------:|:------:|:------:|
|       ^1.0       |   ✅    |   ✅    |   ✅    |   ❌    |   ❌    |   ❌    |   ❌    |   ❌    |   ❌    |
| ^1.6 &#124; ^2.0 |   ❌    |   ✅    |   ✅    |   ✅    |   ✅    |   ❌    |   ❌    |   ❌    |   ❌    |
|       ^3.0       |   ❌    |   ❌    |   ✅    |   ✅    |   ✅    |   ✅    |   ✅    |   ❌    |   ❌    |
|       ^4.0       |   ❌    |   ❌    |   ❌    |   ✅    |   ✅    |   ✅    |   ✅    |   ✅    |   ✅    |


## 📃 Changelog

Please see the [CHANGELOG.md](CHANGELOG.md) for more information
on what has changed recently.

## 🏅 Credits

- [Luca Patera](https://github.com/Lukasss93)
- [All Contributors](https://github.com/Lukasss93/laravel-larex/contributors)

## 📖 License

Please see the [LICENSE.md](LICENSE.md) file for more
information.
