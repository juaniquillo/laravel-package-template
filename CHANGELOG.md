# Changelog

All notable changes to `laravel-package-template` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.3] - 2026-11-19

### Fixed

- `configure.php` removes the stale `composer.lock` written by `composer create-project`, so the
  generated package no longer warns about an out-of-date lock and `composer install` resolves
  against the new package metadata.

## [0.1.2] - 2026-11-19

### Fixed

- `configure.php` derives the default package slug from the current directory, so running it in
  `acme-widgets` produces `acme-widgets` instead of falling back to the template slug.
- The self-configuration guard fails with actionable guidance instead of a confirmation prompt.

### Changed

- `post-create-project-cmd` no longer runs `configure.php`; it prints setup instructions and leaves
  configuration to the user in a normal terminal session.
- `--no-interaction` requires `--vendor`, and `--slug` when it cannot be derived from the directory.
- The template self-test covers directory defaults, printed instructions, missing options and the
  guard.

## [0.1.1] - 2026-11-19

### Added

- `laravel-best-practices` skill for Laravel PHP code, adapted from Laravel Boost.
- `Template` workflow that configures a throwaway copy, runs the gate there, checks that a
  vendor-only rename keeps the author email, and verifies the self-configuration guard.

### Changed

- `composer.json` carries the real description and author instead of the visible
  `:package_description` / `:author_name` placeholders.
- `configure.php` replaces tokens in a single `strtr()` pass so a vendor-only rename cannot rewrite
  the author email, skips `.blade.php` files when sorting imports, and removes the template
  workflow.
- `AGENTS.md` and `README.md` name both AI skills.

## [0.1.0] - 2026-11-19

### Added

- Package skeleton: service provider, Testbench test suite, architecture tests and workbench app.
- `configure.php` to rewrite the skeleton into a real package, with import sorting, hook removal
  and a guard against configuring the template repository itself.
- QA gate: Larastan level 9, Rector, Pint, 100% Pest type coverage and Pest.
- Single GitHub Actions workflow covering PHP 8.3/8.4 and Laravel 12/13.
- `AGENTS.md`, `CLAUDE.md` and package-development skills for Laravel Boost and agent harnesses.
- `GUIDE.md` walkthrough for creating, verifying and releasing a package from the template.
