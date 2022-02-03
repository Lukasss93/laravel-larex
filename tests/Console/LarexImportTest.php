<?php

use Illuminate\Support\Collection;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Console\LarexInitCommand;
use Lukasss93\Larex\Contracts\Importer;
use Lukasss93\Larex\Exceptions\ImportException;

it('does not import strings due to missing importer', function () {
    $this->artisan(LarexImportCommand::class, ['importer' => 'foo'])
        ->expectsOutput("Importer 'foo' not found.")
        ->expectsOutput('')
        ->expectsOutput('Available importers:')
        ->expectsOutput('laravel - Import data from Laravel localization files to CSV')
        ->expectsOutput('json:lang - Import data from JSON by language to CSV')
        ->expectsOutput('json:group - Import data from JSON by group to CSV')
        ->expectsOutput('')
        ->assertExitCode(1);
});

it('does not import strings due to invalid importer', function () {
    config([
        'larex.importers.list.foo' => new class()
        {
        },
    ]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'foo'])
        ->expectsOutput("Importer 'foo' must implements Lukasss93\Larex\Contracts\Importer interface.")
        ->assertExitCode(1);
});

it('does not import strings due to already existing localization file', function () {
    $this->artisan(LarexInitCommand::class);

    $this->artisan(LarexImportCommand::class)
        ->expectsOutput(sprintf("The '%s' already exists.", csv_path(true)))
        ->assertExitCode(1);
});

it('does not import strings due to exception', function () {
    $importer = new class implements Importer
    {
        public static function description(): string
        {
            return 'test import exception';
        }

        public function handle(LarexImportCommand $command): Collection
        {
            throw new ImportException('test exception');
        }
    };

    config(['larex.importers.list.empty' => $importer]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'empty'])
        ->expectsOutput('test exception')
        ->assertExitCode(1);
});

it('does not import strings due to invalid item in collection', function () {
    $importer = new class implements Importer
    {
        public static function description(): string
        {
            return 'test invalid import';
        }

        public function handle(LarexImportCommand $command): Collection
        {
            return collect([
                [
                    'group' => 'app',
                    'key' => 'foobar',
                    'en' => 'foo',
                    'it' => 'bar',
                ],
                null,
            ]);
        }
    };

    config(['larex.importers.list.invalid' => $importer]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'invalid'])
        ->expectsOutput('The item must be an array at index 1.')
        ->assertExitCode(1);
});

it('does not import strings due to invalid group position in collection', function () {
    $importer = new class implements Importer
    {
        public static function description(): string
        {
            return 'test invalid import';
        }

        public function handle(LarexImportCommand $command): Collection
        {
            return collect([
                [
                    'key' => 'foobar',
                    'group' => 'app',
                    'en' => 'foo',
                    'it' => 'bar',
                ],
            ]);
        }
    };

    config(['larex.importers.list.invalid' => $importer]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'invalid'])
        ->expectsOutput("The first key name of the item must be 'group' at index 0.")
        ->assertExitCode(1);
});

it('does not import strings due to invalid key position in collection', function () {
    $importer = new class implements Importer
    {
        public static function description(): string
        {
            return 'test invalid import';
        }

        public function handle(LarexImportCommand $command): Collection
        {
            return collect([
                [
                    'group' => 'app',
                    'en' => 'foo',
                    'key' => 'foobar',
                    'it' => 'bar',
                ],
            ]);
        }
    };

    config(['larex.importers.list.invalid' => $importer]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'invalid'])
        ->expectsOutput("The first key name of the item must be 'key' at index 0.")
        ->assertExitCode(1);
});

it('does not import strings due to missing languages in collection', function () {
    $importer = new class implements Importer
    {
        public static function description(): string
        {
            return 'test invalid import';
        }

        public function handle(LarexImportCommand $command): Collection
        {
            return collect([
                [
                    'group' => 'app',
                    'key' => 'foo',
                ],
            ]);
        }
    };

    config(['larex.importers.list.invalid' => $importer]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'invalid'])
        ->expectsOutput('There must be at least one language code at index 0.')
        ->assertExitCode(1);
});

it('does not import strings due to invalid languages length in collection', function () {
    $importer = new class implements Importer
    {
        public static function description(): string
        {
            return 'test invalid import';
        }

        public function handle(LarexImportCommand $command): Collection
        {
            return collect([
                [
                    'group' => 'app',
                    'key' => 'apple',
                    'en' => 'Apple',
                ],
                [
                    'group' => 'app',
                    'key' => 'car',
                    'en' => 'Car',
                    'it' => 'Auto',
                ],
            ]);
        }
    };

    config(['larex.importers.list.invalid' => $importer]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'invalid'])
        ->expectsOutput('All items in the collection must be the same length at index 1.')
        ->assertExitCode(1);
});

it('does not import strings due to invalid languages position in collection', function () {
    $importer = new class implements Importer
    {
        public static function description(): string
        {
            return 'test invalid import';
        }

        public function handle(LarexImportCommand $command): Collection
        {
            return collect([
                [
                    'group' => 'app',
                    'key' => 'apple',
                    'en' => 'Apple',
                    'it' => 'Mela',
                ],
                [
                    'group' => 'app',
                    'key' => 'car',
                    'it' => 'Auto',
                    'en' => 'Car',
                ],
            ]);
        }
    };

    config(['larex.importers.list.invalid' => $importer]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'invalid'])
        ->expectsOutput('All items in the collection must have the same keys values in the same position at index 1.')
        ->assertExitCode(1);
});

it('does not import strings due to both filled --include and --exclude options', function () {
    $this->artisan(LarexImportCommand::class, ['--include' => 'foo', '--exclude' => 'bar'])
        ->expectsOutput('The --include and --exclude options can be used only one at a time.')
        ->assertExitCode(1);
});

it('imports strings with --force option', function () {
    $this->artisan(LarexInitCommand::class);

    $this->artisan(LarexImportCommand::class, ['--force' => true])
        ->assertExitCode(0);
});

it('imports strings with empty collection', function () {
    $importer = new class implements Importer
    {
        public static function description(): string
        {
            return 'test empty import';
        }

        public function handle(LarexImportCommand $command): Collection
        {
            return collect([]);
        }
    };

    config(['larex.importers.list.empty' => $importer]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'empty'])
        ->expectsOutput('No data found to import.')
        ->assertExitCode(0);
});
