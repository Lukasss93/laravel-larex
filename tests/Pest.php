<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Illuminate\Support\Facades\File;
use Lukasss93\Larex\Support\Utils;
use Lukasss93\Larex\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        //set env to avoid --watch loop
        putenv('NOLOOP=1');

        //set global csv settings
        config([
            'larex.csv' => [
                'path' => lang_path('localization.csv'),
            ],
            'larex.search' => [
                'dirs' => ['resources/views'],
                'patterns' => ['*.php'],
                'functions' => ['__', 'trans', '@lang'],
            ],
            'larex.eol' => "\n",
        ]);

        //clear lang folder
        $items = glob(lang_path('*'));
        foreach ($items as $item) {
            if (is_dir($item)) {
                File::deleteDirectory($item);
            } else {
                File::delete($item);
            }
        }
    })
    ->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('fileContent', fn () => $this->and($this->value = File::get($this->value)));
expect()->extend('toEqualStub', fn (string $name, $eol = "\n") => $this->toEqual(getTestStub($name, $eol)));

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getTestStub(string $name, $eol = "\n"): string
{
    $name = str_replace('.', '/', $name);
    $content = file_get_contents(__DIR__.'/Stubs/'.$name.'.stub');

    return Utils::normalizeEOLs($content, $eol);
}

function initFromStub(string $stub, string $file = null): string
{
    $filePath = Utils::normalizeDS($file ?? csv_path());
    File::put($filePath, getTestStub($stub));

    return $filePath;
}
