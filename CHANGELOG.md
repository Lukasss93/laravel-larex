# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v1.6 - 2021-01-02
### Added
- Added warning message in export command when used with -v option
- Added support for PHP 8

### Fixed
- Config values inaccessible when project is optimized before installing larex
- Missing enclosures when string contains escaped enclosures

## v1.5.1 - 2020-09-15
### Fixed
- Fixed invalid escaping on export command

## v1.5 - 2020-08-29
### Added
- Added **--include** option to `larex:export` command
- Added **--exclude** option to `larex:export` command

## v1.4 - 2020-08-28
### Added
- Added `larex:insert` command

### Fixed
- Fixed `larex --watch` command not working
- Fixed exporting empty column value

## v1.3.1 - 2020-08-11
### Fixed
- Missing language folders creation
- Integer keys are treated as strings

## v1.3 - 2020-08-08
### Added
- Added `larex:export` command (it's an alias of `larex` command)

### Changed
- Deprecated `larex` command (it will be removed in the next major release)

### Fixed
- Sentences with line break aren't escaped.

## v1.2 - 2020-07-18
### Added
- Added `larex:import` command
- Added PHPUnit tests

## v1.1 - 2020-07-07
### Added
- Added line validation

### Fixed
- Failed to parse some rows

## v1.0 - 2020-07-04
### Added
- First release
