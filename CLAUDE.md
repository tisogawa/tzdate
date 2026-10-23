# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

`tzdate` is a PHP command-line tool that displays the current date/time (or a specified date/time) across multiple timezones. It's distributed as a standalone executable built with Box.

## Development Commands

### Build the standalone executable
```bash
./build.sh
```
This script (requires `composer` in PATH):
- Installs Box (PHAR compiler) from `tools/box/composer.lock` and runs `composer audit` on it
- Installs production dependencies from `composer.lock` and runs `composer audit`
- Compiles source into a single `tzdate` executable using `box.json` configuration
- Tests the built executable

### Release

Pushing a `v*` tag runs `.github/workflows/release.yaml`: it runs the tests, builds `tzdate` with `build.sh`, creates a build provenance attestation, and attaches the executable to the GitHub release (creating the release with generated notes if it does not exist yet).

### Install dependencies
```bash
composer install      # Install all dependencies (dev + prod)
composer update       # Update all dependencies
```

### Run tests
```bash
vendor/bin/phpunit                    # Run all tests
vendor/bin/phpunit tests/Console      # Run specific test directory
vendor/bin/phpunit tests/DateTime/DateTimeTest.php  # Run single test file
```

Test configuration is in `phpunit.xml.dist` with bootstrap at `tests/bootstrap.php`.

### Run the tool (development)
```bash
php bin/console                       # Default: current time in default timezones
php bin/console '7/15 9pm' sf        # Specific time and timezone
php bin/console -z hk -z tokyo       # Custom timezone list
php bin/console -f r                 # Custom date format (RFC 2822)
```

## Architecture

### Entry Point Flow
1. `bin/console` - CLI entry point, sets up error handler and instantiates `TzDate\Console\Application`
2. `src/Console/Application.php` - Extends Symfony Console Application, hardcodes 'tzdate' as the default command
3. `src/Console/Command.php` - Main command logic: loads config, parses arguments, displays formatted output

### Custom DateTime Wrappers

The project extends PHP's native DateTime classes to support flexible timezone input:

**`src/DateTime/DateTimeZone.php`**
- Extends `\DateTimeZone` with city name mapping capabilities
- Accepts IANA identifiers (e.g., `America/Los_Angeles`), city names (`Los Angeles`, `losangeles`), or aliases (`sf`)
- Uses static maps (`$cityNamesAndIdentifiersMap`, `$cityNameAliases`) populated from config
- `searchIdentifier()` normalizes input (lowercase, removes spaces/underscores) for flexible matching

**`src/DateTime/DateTime.php`**
- Extends `\DateTime` to track the original city name for display
- Supports Unix timestamps as integer input
- `formatTimezoneOffset()` returns formatted offset string (e.g., `+09:00`, `-07:00`)

### Configuration System

**`src/Config/Config.php`**
- Hierarchical configuration: loads `res/config.json.dist` (defaults), then `res/config.json` (optional overrides)
- Special merge behavior for `cities` and `aliases` keys (merged, not replaced)
- Static `setAdditionalConfig()` allows runtime config injection (used in tests)

**Configuration Keys:**
- `default_timezones`: Array of city names displayed when `-z` option not specified
- `cities`: Map of city names to IANA timezone identifiers for cities not in the IANA database
- `aliases`: Map of abbreviations/alternate names to city names
- `list_format`: sprintf format string for output lines (uses Symfony Console formatting tags)

### Box Configuration

Box itself is a Composer project in `tools/box/` (separate `composer.json`/`composer.lock`, updated by Dependabot), so it does not share dependencies with the application.

`box.json` defines the PHAR compilation:
- Includes `src/`, `res/`, and necessary Symfony vendor files
- Entry point: `bin/console`
- Output: `tzdate` executable
- `git-version: "package_version"` replaces `@package_version@` placeholder in source

## Key Patterns

### Timezone Input Normalization
All timezone input goes through `DateTimeZone::searchIdentifier()`:
1. Builds static lookup tables from IANA identifiers, custom cities, and aliases
2. Normalizes search term (lowercase, removes spaces/underscores)
3. Searches in order: exact IANA identifier → city name → original input (fallback)

### Output Sorting
Results are sorted by UTC offset (ascending) using `array_multisort()` on offset values.

### Error Handling
Custom exceptions for invalid input:
- `InvalidTimeValueException` - unparseable date/time string
- `InvalidTimezoneValueException` - invalid timezone identifier

## Testing

Tests use Symfony Console's `CommandTester` for integration testing:
- `CommandTest` validates command arguments, options, and output
- DateTime tests verify timezone conversion and formatting
- Use `Config::setAdditionalConfig()` to inject test configurations

## PHP Version

Minimum requirement is PHP 8.4 (`require.php` in `composer.json`, README, and the CI matrix 8.4–8.5).
`composer.json` sets `config.platform.php` to `8.4.99` so that `composer.lock` (committed) stays installable on every supported PHP version; the `.99` patch level keeps `roave/security-advisories` from rejecting the platform itself.
Uses modern PHP features: strict types, typed properties, constructor property promotion.