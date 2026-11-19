# Changelog

All notable changes to `laravel-package-template` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-11-19

### Added

- Package skeleton: service provider, Testbench test suite, architecture tests and workbench app.
- `configure.php` to rewrite the skeleton into a real package, with import sorting, hook removal
  and a guard against configuring the template repository itself.
- QA gate: Larastan level 9, Rector, Pint, 100% Pest type coverage and Pest.
- Single GitHub Actions workflow covering PHP 8.3/8.4 and Laravel 12/13.
- `AGENTS.md`, `CLAUDE.md` and package-development skills for Laravel Boost and agent harnesses.
- `GUIDE.md` walkthrough for creating, verifying and releasing a package from the template.
