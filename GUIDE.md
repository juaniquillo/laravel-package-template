# Using the template

This guide walks through turning **laravel-package-template** into a real Laravel package and
keeping it healthy afterwards. It is deleted automatically by `configure.php`, so it never ships
inside your package.

## What you get

- `src/` with an empty, auto-discovered service provider
- Pest (feature, unit, architecture) running on Orchestra Testbench
- A workbench app in `workbench/` for manual poking and test fixtures
- Larastan level 9, Rector, Laravel Pint and a 100% type-coverage gate
- One GitHub Actions workflow covering PHP 8.3/8.4 × Laravel 12/13
- `AGENTS.md`, `CLAUDE.md` and a package-development skill for Laravel Boost and agent harnesses

## Before you start

- PHP `^8.3` and Composer 2
- Git
- A vendor name (the left half of `vendor/package`, lowercase, e.g. `acme`)

## Create the package

### From Packagist (recommended)

```bash
composer create-project juaniquillo/laravel-package-template acme-widgets
cd acme-widgets
```

The install ends with setup instructions. Configure the scaffold by running
`configure.php` yourself:

```bash
php configure.php
```

### Scripted / non-interactive

The hook only prints setup instructions, so no flags are needed to skip it.
Configure with flags, then regenerate the autoloader:

```bash
composer create-project juaniquillo/laravel-package-template acme-widgets
cd acme-widgets
php configure.php \
    --vendor=acme \
    --slug=acme-widgets \
    --name="Acme Widgets" \
    --namespace="Acme\\Widgets" \
    --author="Jane Doe" \
    --email=jane@example.com \
    --description="Widgets for Acme." \
    --no-interaction
composer install
```

`composer install` is required after configuring an already-installed copy: it regenerates the
autoloader for the new namespace.

### From a clone or GitHub's "Use this template"

```bash
git clone https://github.com/juaniquillo/laravel-package-template.git acme-widgets
cd acme-widgets
php configure.php
composer install
composer qa
```

## Answering `configure.php`

| Prompt | Flag | Default | Notes |
|---|---|---|---|
| Vendor slug | `--vendor` | `juaniquillo` | Composer vendor, lowercase |
| Package slug | `--slug` | derived from the target directory (`acme-widgets`) | Composer package name |
| Package name | `--name` | derived from the slug (`Acme Widgets`) | Human-readable |
| Namespace | `--namespace` | derived (`Vendor\Package`) | Backslash-separated |
| Author name | `--author` | `Victor Sánchez` | Used in `LICENSE.md` and `composer.json` |
| Author email | `--email` | `juaniquillo@gmail.com` | Used in `composer.json` |
| Description | `--description` | `Opinionated starting point for a Laravel package.` | Composer description |
| Copyright year | `--year` | current year | Used in `LICENSE.md` |

Defaults come from the directory you are in: creating `acme-widgets` makes `acme-widgets` the
default slug, `Acme Widgets` the default name and `Juaniquillo\AcmeWidgets` the default namespace.
The vendor still defaults to `juaniquillo`, so set `--vendor` or answer that prompt with your own.

With `--no-interaction`, `--vendor` is required; `--slug` is required only when it cannot be
derived from the directory name (for example when the directory is named `laravel-package-template`)
or when it would still point at the template. Missing options are reported before anything is
written.

`configure.php` also refuses to run when every value still identifies the template itself. Pass
your own vendor and slug, or `--force` when maintaining the template.

Derived values:

- **Namespace** = studly vendor + studly slug (`acme` + `acme-widgets` → `Acme\AcmeWidgets`)
- **Provider** = studly slug + `ServiceProvider` (`AcmeWidgetsServiceProvider`)

Other options:

- `--no-interaction`, `-n` — never prompt; use flags and defaults
- `--instructions` — print setup instructions and exit (used by the post-create hook)
- `--keep-configure` — do not delete `configure.php`
- `--force`, `-f` — allow configuring the template repository itself (maintenance only)
- `--help`, `-h`

The script refuses to run when vendor, slug, namespace and name are all still the template's,
unless `--force` is passed.

## What gets rewritten

| Item | Where |
|---|---|
| Composer name, homepage, keywords | `composer.json` |
| Description, author name/email | `composer.json` |
| Root namespace | `composer.json` autoload, `src/`, `tests/`, `tests/ArchTest.php` |
| Provider class and filename | `src/*ServiceProvider.php`, `composer.json` `extra.laravel.providers`, tests, README |
| Package title and badges | `README.md` |
| Install command, provider reference, structure table | `README.md` |
| Copyright year and author | `LICENSE.md` |
| Skill name and directories | `.agents/skills/…`, `resources/boost/skills/…` |
| Changelog | `CHANGELOG.md` reset to `## [Unreleased]` |
| Removed files | `configure.php`, `GUIDE.md`, `.github/workflows/template.yml`, `composer.lock`, README template section, `post-create-project-cmd` hook |

## After configuring

1. `composer install` — regenerates the autoloader for the new namespace. The stale
   `composer.lock` from `composer create-project` is removed during configuration, so this also
   re-resolves dependencies against the new package metadata.
2. `composer qa` — must be green before you touch anything else.
3. Replace the `Usage` block in `README.md` with real documentation.
4. Add your first `CHANGELOG.md` entry under `[Unreleased]`.
5. Point the repository at your remote and push.
6. Set the GitHub description and topics (`laravel`, plus your package's domain).
7. Protect `main` and require the CI workflow.

## Day-to-day workflow

| Command | Purpose |
|---|---|
| `composer qa` | Full gate: analyse → rector → lint:check → test:types → test:unit |
| `composer test:unit` | Pest only (no `--parallel` on Windows, `--ci` in Actions) |
| `composer test:types` | Type coverage, gated at 100% |
| `composer analyse` | PHPStan/Larastan level 9 on `src/` |
| `composer lint` / `lint:check` | Pint write / verify |
| `composer rector` / `rector:check` | Rector write / verify |
| `composer build` / `serve` | Build and serve the workbench app |
| `composer prepare` / `clear` | Package discovery / purge the cached manifest |

Adding a capability:

1. Add the class under `src/` (one class per file, strict types, typed properties and returns).
2. Register bindings, commands, migrations or views in the service provider only when needed.
3. Add a feature test under `tests/Feature`; add an architecture rule if the boundary matters.
4. Run `composer qa`, then update `README.md` and `CHANGELOG.md`.

## Releasing

1. Move `[Unreleased]` entries to a version heading with today's date.
2. Tag and push: `git tag v0.1.0 && git push --tags`.
3. Submit the repository on Packagist once; subsequent tags publish automatically.
4. Consumers install with `composer require vendor/package`.

Packagist needs a stable tag (`v1.0.0`, `v0.1.0`, …) before `composer require` resolves without
`dev-main`; pre-release tags like `v0.1.0-beta` stay opt-in.

## CI

The workflow in `.github/workflows/ci.yml` runs on pull requests only and covers PHP 8.3/8.4 with
Laravel 12/13, executing the same five `composer` scripts as `qa`. Composer resolves Pest 5 where
the platform allows it and Pest 4 on PHP 8.3; both majors are inside the constraint range, so CI is
the place that proves the PHP 8.3 combination.

Run `composer qa` locally before opening the pull request — it is the same gate.

A second workflow, `Template`, is template-only and is deleted when you configure: it configures a
throwaway copy, asserts the script, guide and workflow are gone with no placeholders left, runs
`composer qa` in the copy, checks that a vendor-only rename keeps the author email intact, and
verifies the self-configuration guard.

## Troubleshooting

**Type coverage below 100%** — add native parameter, return and property types. Do not lower
`--min`; the gate is what keeps `src/` honest.

**Pint fails on `ordered_imports`** — run `composer lint`. This is also why `configure.php` sorts
`use` blocks after renaming the namespace.

**`package:purge-skeleton` / `package:discover` errors** — run `composer clear` then
`composer prepare`. Both run automatically on `post-autoload-dump`.

**Pest is slow or fails on Windows** — `bin/pest.php` disables `--parallel` there because parallel
workers race over the shared Testbench skeleton.

**`configure.php` refuses to run** — the values still identify the template (for example when the
directory is named `laravel-package-template`). Pass your own `--vendor` and `--slug`, or `--force`
when maintaining the template. With `--no-interaction`, `--vendor` is always required.

**Configured with the wrong values** — the script is gone, so fix these by hand: `composer.json`
(name, homepage, keywords, authors, description, autoload, `extra.laravel.providers`), the service
provider filename and class, `tests/TestCase.php`, `tests/Feature/ServiceProviderTest.php`,
`tests/Pest.php`, `tests/ArchTest.php`, `README.md`, `LICENSE.md`, `CHANGELOG.md`, and the skill
directories plus their `name:` frontmatter.

**Tabs, CRLF or missing final newline** — run `composer lint`; `.editorconfig` and Pint keep the
repository consistent.

## Syncing with the template

Template updates are safest copied by hand. Compare the tooling files and port what you want:

```bash
git remote add template https://github.com/juaniquillo/laravel-package-template.git
git fetch template
git diff template/main -- pint.json phpstan.neon.dist phpunit.xml.dist rector.php \
    bin/pest.php .github/workflows/ci.yml tests/ArchTest.php
```

Avoid merging the template branch wholesale: your package's `src/`, `tests/`, `README.md` and
`composer.json` have intentionally diverged. Run `composer qa` after any port.
