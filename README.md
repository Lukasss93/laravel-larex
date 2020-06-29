<center>
<img src="https://i.imgur.com/CddZo0R.png"/>
</center>

# Laravel Larex

WIP

## Work In Progress
This package is still under active development.

## Installation
You can install the package using composer

```bash
composer require lukasss93/laravel-larex  
```

Then add the service provider to `config/app.php`.  
In Laravel versions 5.5 and beyond, this step can be skipped if package auto-discovery is enabled.

```php
'providers' => [
    ...
    Lukasss93\Larex\LarexServiceProvider::class
    ...
];
```

Now that we have published a few new files to our application we need to reload them with the following command:

```bash
composer dump-autoload
```

## Usage

WIP

## Changelog

Please see the CHANGELOG.md for more information on what has changed recently.

## License

Please see the license file for more information.
