# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Please check the [UPGRADING.md](https://github.com/Lukasss93/laravel-larex/blob/master/UPGRADING.md)
file before upgrading to a major version!

## [v3.3](https://github.com/Lukasss93/laravel-larex/releases/tag/v3.3) - 2021-08-03

### Added

- Added a new column in larex:find to preview the first translated value in the csv file

## [v3.2](https://github.com/Lukasss93/laravel-larex/releases/tag/v3.2) - 2021-07-01

### Added

- Added `larex:remove` command

### Fixed

- Fix wrong separator in stubs files
- Fix wrong header after larex:insert command

## [v3.1](https://github.com/Lukasss93/laravel-larex/releases/tag/v3.1) - 2021-05-22
### Added
- Added `larex:find` command
- Added `eol` option to config

## [v3.0.1](https://github.com/Lukasss93/laravel-larex/releases/tag/v3.0.1) - 2021-04-12
### Fixed
- Fix larex:insert always exports strings

## [v3.0](https://github.com/Lukasss93/laravel-larex/releases/tag/v3.0) - 2021-04-11
### Added
- Added Exporters
- Added Importers
- Added possibility to change exporter in `larex:insert` command with `--export=<exporter>` option
- Added `larex:localize` command to find unlocalized strings

### Changed
- Now the `larex:export` command optionally accepts an exporter as argument
- Now the `larex:import` command optionally accepts an importer as argument
- Changed Linter interface signature

### Removed
- Removed `larex` command
- Removed csv options from larex config: delimiter, enclosure, escape
- Dropped PHP 7.3 support
- Dropped Laravel 6 support

## [v2.1](https://github.com/Lukasss93/laravel-larex/releases/tag/v2.1) - 2021-04-03
### Added
- Added a confirmation in larex:insert command
- Added check if group/key already exists when using the larex:insert command

### Fixed
- Fix null values to group and key questions in larex:insert command
- Fix "Insert command has problems with utf-8 characters"
- Fix "Wrong enclosure with larex:insert"

## [v2.0.1](https://github.com/Lukasss93/laravel-larex/releases/tag/v2.0.1) - 2021-03-11
### Changed
- Forced eol to LF

## [v2.0](https://github.com/Lukasss93/laravel-larex/releases/tag/v2.0) - 2021-01-09
### Added
- Added new command `php artisan larex:lint`

### Changed
- Changed default csv format:
    - Delimiter changed from `;` to `,`
    - Escape character changed from `\"` to `""`


## [v1.6](https://github.com/Lukasss93/laravel-larex/releases/tag/v1.6) - 2021-01-02
### Added
- Added warning message in export command when used with -v option
- Added support for PHP 8

### Fixed
- Config values inaccessible when project is optimized before installing larex
- Missing enclosures when string contains escaped enclosures

### Removed
- Dropped support for PHP 7.2


## [v1.5.1](https://github.com/Lukasss93/laravel-larex/releases/tag/v1.5.1) - 2020-09-15
### Fixed
- Fixed invalid escaping on export command


## [v1.5](https://github.com/Lukasss93/laravel-larex/releases/tag/v1.5) - 2020-08-29
### Added
- Added **--include** option to `larex:export` command
- Added **--exclude** option to `larex:export` command


## [v1.4](https://github.com/Lukasss93/laravel-larex/releases/tag/v1.4) - 2020-08-28
### Added
- Added `larex:insert` command

### Fixed
- Fixed `larex --watch` command not working
- Fixed exporting empty column value


## [v1.3.1](https://github.com/Lukasss93/laravel-larex/releases/tag/v1.3.1) - 2020-08-11
### Fixed
- Missing language folders creation
- Integer keys are treated as strings


## [v1.3](https://github.com/Lukasss93/laravel-larex/releases/tag/v1.3) - 2020-08-08
### Added
- Added `larex:export` command (it's an alias of `larex` command)

### Changed
- Deprecated `larex` command (it will be removed in the next major release)

### Fixed
- Sentences with line break aren't escaped.


## [v1.2](https://github.com/Lukasss93/laravel-larex/releases/tag/v1.2) - 2020-07-18
### Added
- Added `larex:import` command
- Added PHPUnit tests


## [v1.1](https://github.com/Lukasss93/laravel-larex/releases/tag/v1.1) - 2020-07-07
### Added
- Added line validation

### Fixed
- Failed to parse some rows


## [v1.0](https://github.com/Lukasss93/laravel-larex/releases/tag/v1.0) - 2020-07-04
### Added
- First release
