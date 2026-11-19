---
name: laravel-package-template-development
description: Work on the laravel-package-template Laravel package — add classes and service provider bindings, write Pest feature, unit and architecture tests, extend the Testbench workbench app, and run the analyse/rector/lint/type-coverage/Pest QA gate.
---

# Laravel Package Template Development

## When to use this skill

Use this skill when the task touches this repository:

- Add or change a class, contract, enum or trait in `src/`
- Register bindings, publishes, commands, migrations or views in the service provider
- Write or update Pest tests (feature, unit or architecture)
- Add routes, models, migrations, factories or seeders to the `workbench/` app
- Fix PHPStan, Rector, Pint, type-coverage or test failures

## Guardrail commands

Run from the repository root, in this order:

```bash
composer qa         # analyse → rector:check → lint:check → test:types → test:unit
composer lint       # rewrite code style
composer rector     # apply automated refactors
composer test:unit  # Pest only
```

`composer qa` must pass before a change is considered done. Never weaken the gate to make it pass.

## Layout

| Path | Purpose |
|---|---|
| `src/` | Package source, `Juaniquillo\LaravelPackageTemplate` |
| `tests/Feature/` | Pest feature tests (Testbench boots a Laravel app) |
| `tests/Unit/` | Pest unit tests (no application boot) |
| `tests/ArchTest.php` | Architecture rules: presets, strict types, banned helpers |
| `workbench/` | Disposable Laravel app for manual checks and fixtures |
| `testbench.yaml` | Workbench wiring (providers, migrations, seeders, build steps) |
| `bin/pest.php` | Pest runner: parallel outside Windows, `--ci` in GitHub Actions |

## Conventions

- `declare(strict_types=1)` at the top of every PHP file.
- Namespace the package as `Juaniquillo\LaravelPackageTemplate`; tests as
  `Juaniquillo\LaravelPackageTemplate\Tests`.
- One class per file. Methods and properties are typed — type coverage is 100%, so an untyped
  parameter, return value or property fails `composer test:types`.
- Prefer constructor injection and Laravel contracts; only reach for facades in the workbench.
- Do not use `dd()`, `ddd()`, `env()` or `exit()` anywhere — `ArchTest` fails.
- New public behaviour needs a feature test and a `CHANGELOG.md` entry under `## [Unreleased]`.

## Adding a class

```php
<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelPackageTemplate;

final class Example
{
    public function __construct(
        private readonly string $name,
    ) {}
}
```

## Registering it

Expose the class through `LaravelPackageTemplateServiceProvider` only when the package needs
container bindings, commands, migrations, views or config publishing:

```php
public function register(): void
{
    $this->app->singleton(Example::class);
}
```

## Testing

Feature test that boots the package provider:

```php
it('resolves the example', function () {
    expect($this->app->make(Example::class))->toBeInstanceOf(Example::class);
});
```

Unit test with no application boot lives in `tests/Unit` and extends `Tests\TestCase` as well, so
helpers such as `expect()` work as usual.
