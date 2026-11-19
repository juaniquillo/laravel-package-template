# Laravel Package Template

[![CI](https://github.com/juaniquillo/laravel-package-template/actions/workflows/ci.yml/badge.svg)](https://github.com/juaniquillo/laravel-package-template/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/github/release/juaniquillo/laravel-package-template.svg)](https://github.com/juaniquillo/laravel-package-template/releases)
[![License](https://img.shields.io/github/license/juaniquillo/laravel-package-template.svg)](LICENSE.md)
[![PHP](https://img.shields.io/badge/php-%5E8.3-777bb4.svg)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/laravel-12%7C13-ff2d20.svg)](https://laravel.com)

<!-- template:start -->
An opinionated starting point for a Laravel package: **Pest**, **Larastan level 9**, **Rector**,
**Laravel Pint**, Orchestra Testbench with a workbench app, a single GitHub Actions matrix, and
AI-first documentation (`AGENTS.md` plus skills for Laravel Boost and other agent harnesses).

## Using this template

Read **[GUIDE.md](GUIDE.md)** for the full walkthrough: creating a package, what `configure.php`
rewrites, the post-setup checklist, releasing, CI and troubleshooting.

Quick start:

```bash
composer create-project juaniquillo/laravel-package-template acme-widgets
cd acme-widgets
php configure.php
```

Then install and verify:

```bash
composer install
composer qa
```
<!-- template:end -->

## Requirements

- PHP `^8.3`
- Laravel `12` or `13` (used by Testbench; the package itself only requires `illuminate/contracts` and `illuminate/support`)

## Installation

```bash
composer require juaniquillo/laravel-package-template
```

The service provider is auto-discovered:

```php
Juaniquillo\LaravelPackageTemplate\LaravelPackageTemplateServiceProvider::class
```

## Usage

```php
// Document the package's public API here.
```

## Testing

```bash
composer qa         # analyse → rector:check → lint:check → test:types → test:unit
composer test:unit  # Pest only (no --parallel on Windows)
composer test:types # 100% type coverage gate
```

## Workbench

A disposable Laravel app lives in `workbench/`:

```bash
composer build
composer serve
```

## AI skills

```bash
php artisan boost:add-skill https://github.com/juaniquillo/laravel-package-template
```

## Structure

| Path | Purpose |
|---|---|
| `src/` | Package source (`Juaniquillo\LaravelPackageTemplate`) |
| `tests/` | Pest feature, unit and architecture tests |
| `workbench/` | Disposable Laravel app powered by Orchestra Testbench |
| `.agents/skills/`, `resources/boost/skills/` | AI skills for Laravel code and for working on this package |

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Contributing

Run `composer qa` before opening a pull request.

## License

MIT. See [LICENSE.md](LICENSE.md).
