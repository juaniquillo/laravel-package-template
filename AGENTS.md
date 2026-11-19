# Laravel Package Template

An opinionated Laravel package, kept small, idiomatic, and easy to install, test and maintain.

## Commands

- `composer qa` — full gate: `analyse → rector:check → lint:check → test:types → test:unit`
- `composer test:unit` — Pest (adds `--parallel` outside Windows and `--ci` on GitHub Actions)
- `composer test:types` — Pest type coverage, pinned at 100%
- `composer analyse` — PHPStan/Larastan at level 9
- `composer lint` / `composer lint:check` — Pint (write / verify)
- `composer rector` / `composer rector:check` — Rector (write / verify)
- `composer prepare` — package discovery after changing dependencies
- `composer clear` — purge the cached package manifest
- `composer build` / `composer serve` — build and serve the workbench app

`composer qa` must be green before a pull request.

## Conventions

- `declare(strict_types=1)` in every PHP file; Pint enforces it.
- Package code lives under `Juaniquillo\LaravelPackageTemplate` (`src/`); tests under
  `Juaniquillo\LaravelPackageTemplate\Tests`.
- One class per file, PSR-4, no helpers or facades until the package actually needs them.
- Prefer Laravel contracts and constructor injection over facades inside `src/`.
- Public API changes need tests and a CHANGELOG entry.
- Keep `composer.json` metadata, the provider name and README badges aligned.

## Testing

- Feature tests extend `Tests\TestCase` (Orchestra Testbench) and use Pest's `it()` syntax.
- Test observable behaviour through public APIs; avoid asserting implementation details.
- `tests/ArchTest.php` bans `dd()`, `ddd()`, `env()` and `exit()`, and requires strict types.
- Type coverage is gated at 100% — type every parameter, return value and property.

## Workbench

`workbench/` is a disposable Laravel app wired through `testbench.yaml`. Use it to poke at the
package manually and to back feature tests with routes, models and migrations. Files there follow
the `Workbench\` namespace and are never shipped.

## Structure

| Path | Purpose |
|---|---|
| `src/` | Package source |
| `tests/` | Pest tests (Arch, Feature, Unit) |
| `workbench/` | Testbench application |
| `.agents/skills/`, `resources/boost/skills/` | AI skills: `laravel-best-practices` for Laravel code, `laravel-package-template-development` for this repository |
| `bin/pest.php` | Cross-platform Pest runner |

## Adding a capability

1. Add the class under `src/` (and its tests under `tests/Feature`).
2. Expose it through the service provider only when it needs container bindings or publishing.
3. Run `composer qa`.
4. Update `README.md` usage and `CHANGELOG.md`.
