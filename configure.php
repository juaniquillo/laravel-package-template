#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * Rewrites this skeleton into a real Laravel package.
 *
 * Usage:
 *   php configure.php
 *   php configure.php --vendor=acme --name="Acme Widgets" --slug=acme-widgets --no-interaction
 */

const TEMPLATE_VENDOR = 'juaniquillo';
const TEMPLATE_NAMESPACE = 'Juaniquillo\\LaravelPackageTemplate';
const TEMPLATE_SERVICE_PROVIDER = 'LaravelPackageTemplateServiceProvider';
const TEMPLATE_PACKAGE_NAME = 'Laravel Package Template';
const TEMPLATE_PACKAGE_SLUG = 'laravel-package-template';
const TEMPLATE_AUTHOR = 'Victor Sánchez';
const TEMPLATE_AUTHOR_EMAIL = 'juaniquillo@gmail.com';
const TEMPLATE_DESCRIPTION = 'Opinionated starting point for a Laravel package.';

const SKILL_DIRECTORIES = [
    '.agents/skills',
    'resources/boost/skills',
];

const SKIP_DIRECTORIES = [
    '.git',
    '.idea',
    '.phpunit.cache',
    '.vscode',
    'build',
    'coverage',
    'node_modules',
    'vendor',
];

const SKIP_FILES = [
    'configure.php',
    'GUIDE.md',
    'template.yml',
];

const REMOVED_FILES = [
    '.github/workflows/template.yml',
    'GUIDE.md',
    'composer.lock',
];

const TEXT_EXTENSIONS = [
    'dist',
    'editorconfig',
    'example',
    'gitattributes',
    'gitignore',
    'json',
    'md',
    'neon',
    'php',
    'stub',
    'txt',
    'xml',
    'yaml',
    'yml',
];

const BOOLEAN_OPTIONS = [
    'force',
    'help',
    'instructions',
    'keep-configure',
    'no-interaction',
];

const SHORT_OPTIONS = [
    '-f' => 'force',
    '-h' => 'help',
    '-n' => 'no-interaction',
];

$root = __DIR__;

$options = parseOptions(array_slice($_SERVER['argv'] ?? [], 1));

if (isset($options['help'])) {
    printHelp();

    exit(0);
}

if (isset($options['instructions'])) {
    printInstructions();

    exit(0);
}

$interactive = ! isset($options['no-interaction']);

writeln('');
writeln(bold('Laravel package template'));
writeln(dim('Press Enter to accept the default value.'));
writeln('');

$vendorSlug = ask($options, 'vendor', 'Vendor slug', TEMPLATE_VENDOR, $interactive, 'e.g. "acme"');
validateSlug('Vendor slug', $vendorSlug);

$defaultPackageSlug = defaultPackageSlug($root);

$packageSlug = ask($options, 'slug', 'Package slug', $defaultPackageSlug, $interactive, 'e.g. "acme-widgets"');
validateSlug('Package slug', $packageSlug);

$packageName = ask($options, 'name', 'Package name', headline($packageSlug), $interactive, 'e.g. "Acme Widgets"');

$namespace = ask($options, 'namespace', 'Namespace', studly($vendorSlug).'\\'.studly($packageSlug), $interactive);
validateNamespace($namespace);

$authorName = ask($options, 'author', 'Author name', TEMPLATE_AUTHOR, $interactive);
$authorEmail = ask($options, 'email', 'Author email', TEMPLATE_AUTHOR_EMAIL, $interactive);
validateEmail($authorEmail);

$description = ask($options, 'description', 'Description', TEMPLATE_DESCRIPTION, $interactive);
$year = ask($options, 'year', 'Copyright year', date('Y'), $interactive);
validateYear($year);

$isTemplateItself = $vendorSlug === TEMPLATE_VENDOR
    && $packageSlug === TEMPLATE_PACKAGE_SLUG
    && $namespace === TEMPLATE_NAMESPACE
    && $packageName === TEMPLATE_PACKAGE_NAME;

if (! $interactive) {
    $missing = [];

    if (! isset($options['vendor'])) {
        $missing[] = '--vendor';
    }

    if (! isset($options['slug']) && $defaultPackageSlug === TEMPLATE_PACKAGE_SLUG) {
        $missing[] = '--slug';
    }

    if ($missing !== []) {
        fail(
            'Missing required option(s) for --no-interaction: '.implode(', ', $missing).'.',
            'Example: php configure.php --vendor=acme --slug=acme-widgets --no-interaction',
        );
    }
}

if ($isTemplateItself && ! isset($options['force'])) {
    fail(
        'These values still point at the template itself, so there is nothing to rename.',
        'Answer the prompts with your own vendor and package name, or pass --vendor=acme --slug=acme-widgets.',
        'Use --force only when maintaining the template repository.',
    );
}

$providerClass = studly($packageSlug).'ServiceProvider';

$replacements = [
    'juaniquillo@gmail.com' => $authorEmail,
    'Victor Sánchez' => $authorName,
    'Opinionated starting point for a Laravel package.' => $description,
    TEMPLATE_VENDOR => $vendorSlug,
    TEMPLATE_NAMESPACE => $namespace,
    TEMPLATE_SERVICE_PROVIDER => $providerClass,
    TEMPLATE_PACKAGE_NAME => $packageName,
    TEMPLATE_PACKAGE_SLUG => $packageSlug,
    ':author_name' => $authorName,
    ':year' => $year,
];

writeln('');
writeln(bold('Rewriting files'));
writeln('');

$changed = [];

foreach (collectFiles($root) as $path) {
    $updated = replaceInFile($path, $replacements);

    if (sortImports($path)) {
        $updated = true;
    }

    if ($updated) {
        $changed[] = relativePath($root, $path);
    }
}

foreach ($changed as $file) {
    writeln('  '.green('updated').' '.$file);
}

stripTemplateSection($root.'/README.md');
resetChangelog($root.'/CHANGELOG.md', $packageSlug);
removeCreateProjectHook($root.'/composer.json');

$sourceProvider = $root.'/src/'.TEMPLATE_SERVICE_PROVIDER.'.php';
$targetProvider = $root.'/src/'.$providerClass.'.php';

if (is_file($sourceProvider) && $sourceProvider !== $targetProvider) {
    rename($sourceProvider, $targetProvider);

    writeln('  '.yellow('renamed').' src/'.TEMPLATE_SERVICE_PROVIDER.'.php → src/'.$providerClass.'.php');
}

foreach (SKILL_DIRECTORIES as $directory) {
    $source = $root.'/'.$directory.'/'.TEMPLATE_PACKAGE_SLUG.'-development';
    $target = $root.'/'.$directory.'/'.$packageSlug.'-development';

    if (is_dir($source) && $source !== $target) {
        rename($source, $target);

        writeln('  '.yellow('renamed').' '.$directory.'/'.TEMPLATE_PACKAGE_SLUG.'-development');
    }
}

foreach (REMOVED_FILES as $file) {
    $path = $root.'/'.$file;

    if (is_file($path)) {
        unlink($path);

        writeln('  '.yellow('removed').' '.$file);
    }
}

writeln('');
writeln(green('Package configured.'));
writeln('');
writeln('  Composer name: '.$vendorSlug.'/'.$packageSlug);
writeln('  Namespace:     '.$namespace);
writeln('  Provider:      '.$namespace.'\\'.$providerClass);
writeln('');

if (isset($options['keep-configure'])) {
    writeln(dim('configure.php was kept. Remove it once you are happy with the result.'));
} else {
    unlink(__FILE__);

    writeln(dim('configure.php removed.'));
}

writeln('');
writeln(bold('Next steps'));
writeln('');
writeln('  composer install');
writeln('  composer qa');
writeln('');

/**
 * @param  list<string>  $arguments
 * @return array<string, string|bool>
 */
function parseOptions(array $arguments): array
{
    $options = [];

    while ($arguments !== []) {
        $argument = array_shift($arguments);

        if (isset(SHORT_OPTIONS[$argument])) {
            $options[SHORT_OPTIONS[$argument]] = true;

            continue;
        }

        if (! str_starts_with($argument, '--')) {
            fail('Unexpected argument: '.$argument);
        }

        $option = substr($argument, 2);

        if (in_array($option, BOOLEAN_OPTIONS, true)) {
            $options[$option] = true;

            continue;
        }

        [$name, $value] = str_contains($option, '=')
            ? explode('=', $option, 2)
            : [$option, null];

        if ($value === null) {
            $value = array_shift($arguments);
        }

        if ($value === null || $value === '') {
            fail('Missing value for --'.$name);
        }

        $options[$name] = $value;
    }

    return $options;
}

/**
 * @param  array<string, string|bool>  $options
 */
function ask(array $options, string $key, string $label, string $default, bool $interactive, string $hint = ''): string
{
    if (isset($options[$key]) && is_string($options[$key])) {
        return $options[$key];
    }

    if (! $interactive) {
        return $default;
    }

    if ($hint !== '') {
        writeln(dim('  '.$hint));
    }

    $answer = trim(prompt('  '.bold($label).' ['.$default.']: '));

    return $answer === '' ? $default : $answer;
}

function prompt(string $message): string
{
    if (function_exists('readline')) {
        return (string) readline($message);
    }

    fwrite(STDOUT, $message);

    $answer = fgets(STDIN);

    return $answer === false ? '' : $answer;
}

/**
 * @param  array<string, string>  $replacements
 */
function replaceInFile(string $path, array $replacements): bool
{
    $contents = file_get_contents($path);

    if ($contents === false) {
        return false;
    }

    $replacements = str_ends_with($path, '.json')
        ? array_combine(
            array_map('jsonEscape', array_keys($replacements)),
            array_map('jsonEscape', array_values($replacements)),
        )
        : $replacements;

    $updated = strtr($contents, $replacements);

    if ($updated === $contents) {
        return false;
    }

    return file_put_contents($path, $updated) !== false;
}

function sortImports(string $path): bool
{
    if (! str_ends_with($path, '.php') || str_ends_with($path, '.blade.php')) {
        return false;
    }

    $contents = file_get_contents($path);

    if ($contents === false) {
        return false;
    }

    $eol = str_contains($contents, "\r\n") ? "\r\n" : "\n";
    $lines = explode($eol, $contents);
    $result = [];
    $block = [];

    $flush = static function () use (&$result, &$block): void {
        if ($block === []) {
            return;
        }

        sort($block, SORT_STRING);

        array_push($result, ...$block);

        $block = [];
    };

    foreach ($lines as $line) {
        if (preg_match('/^use\s+[^;]+;$/', $line) === 1) {
            $block[] = $line;

            continue;
        }

        $flush();
        $result[] = $line;
    }

    $flush();

    $updated = implode($eol, $result);

    if ($updated === $contents) {
        return false;
    }

    return file_put_contents($path, $updated) !== false;
}

/**
 * @return list<string>
 */
function collectFiles(string $root): array
{
    $files = [];

    $directories = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
            static function (SplFileInfo $file): bool {
                return ! ($file->isDir() && in_array($file->getFilename(), SKIP_DIRECTORIES, true));
            },
        ),
    );

    foreach ($directories as $file) {
        if (! $file instanceof SplFileInfo || ! $file->isFile()) {
            continue;
        }

        if (in_array($file->getFilename(), SKIP_FILES, true)) {
            continue;
        }

        if (in_array(strtolower($file->getExtension()), TEXT_EXTENSIONS, true)) {
            $files[] = $file->getPathname();
        }
    }

    sort($files);

    return $files;
}

function stripTemplateSection(string $readme): void
{
    if (! is_file($readme)) {
        return;
    }

    $contents = file_get_contents($readme);

    if ($contents === false) {
        return;
    }

    $stripped = preg_replace(
        '/<!-- template:start -->.*?<!-- template:end -->\n*/s',
        '',
        $contents,
    );

    if (is_string($stripped) && $stripped !== $contents) {
        file_put_contents($readme, $stripped);
    }
}

function resetChangelog(string $changelog, string $packageSlug): void
{
    $contents = <<<MD
    # Changelog

    All notable changes to `{$packageSlug}` will be documented in this file.

    The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
    and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

    ## [Unreleased]

    MD;

    file_put_contents($changelog, $contents."\n");
}

function removeCreateProjectHook(string $composerJson): void
{
    if (! is_file($composerJson)) {
        return;
    }

    $contents = file_get_contents($composerJson);

    if ($contents === false) {
        return;
    }

    $composer = json_decode($contents, true);

    if (! is_array($composer) || ! isset($composer['scripts']['post-create-project-cmd'])) {
        return;
    }

    unset($composer['scripts']['post-create-project-cmd']);

    $encoded = json_encode(
        $composer,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
    );

    if ($encoded === false) {
        return;
    }

    file_put_contents($composerJson, $encoded."\n");
}

function validateSlug(string $label, string $value): void
{
    if (preg_match('/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/', $value) !== 1) {
        fail('Invalid '.strtolower($label).' "'.$value.'": use lowercase letters, digits, dots, dashes or underscores.');
    }
}

function validateNamespace(string $value): void
{
    if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*(?:\\\\[A-Za-z_][A-Za-z0-9_]*)*$/', $value) !== 1) {
        fail('Invalid namespace "'.$value.'": use a backslash-separated PHP namespace.');
    }
}

function validateEmail(string $value): void
{
    if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
        fail('Invalid author email "'.$value.'".');
    }
}

function validateYear(string $value): void
{
    if (preg_match('/^\d{4}$/', $value) !== 1) {
        fail('Invalid copyright year "'.$value.'".');
    }
}

function studly(string $value): string
{
    $value = str_replace(' ', '', ucwords(str_replace(['-', '_', '.'], ' ', $value)));

    return $value;
}

function defaultPackageSlug(string $root): string
{
    $directory = slugify(basename($root));

    if ($directory === '' || $directory === TEMPLATE_PACKAGE_SLUG) {
        return TEMPLATE_PACKAGE_SLUG;
    }

    return $directory;
}

function slugify(string $value): string
{
    $value = (string) preg_replace('/(?<=[a-z0-9])(?=[A-Z])/', '-', $value);
    $value = strtolower($value);
    $value = (string) preg_replace('/[^a-z0-9]+/', '-', $value);

    return trim($value, '-');
}

function headline(string $value): string
{
    return ucwords(str_replace(['-', '_', '.'], ' ', $value));
}

function jsonEscape(string $value): string
{
    return str_replace(
        ['\\', '"'],
        ['\\\\', '\\"'],
        $value,
    );
}

function relativePath(string $root, string $path): string
{
    return str_replace('\\', '/', substr($path, strlen($root) + 1));
}

function supportsAnsi(): bool
{
    if (getenv('NO_COLOR') !== false) {
        return false;
    }

    if (PHP_OS_FAMILY === 'Windows') {
        return (function_exists('sapi_windows_vt100_support') && sapi_windows_vt100_support(STDOUT))
            || getenv('ANSICON') !== false
            || getenv('ConEmuANSI') === 'ON'
            || str_starts_with((string) getenv('TERM'), 'xterm');
    }

    return stream_isatty(STDOUT);
}

function ansi(string $text, string $code): string
{
    return supportsAnsi() ? "\033[".$code.'m'.$text."\033[0m" : $text;
}

function bold(string $text): string
{
    return ansi($text, '1');
}

function dim(string $text): string
{
    return ansi($text, '2');
}

function green(string $text): string
{
    return ansi($text, '32');
}

function yellow(string $text): string
{
    return ansi($text, '33');
}

function writeln(string $line): void
{
    echo $line.PHP_EOL;
}

function fail(string ...$lines): never
{
    foreach ($lines as $line) {
        fwrite(STDERR, $line.PHP_EOL);
    }

    fwrite(STDERR, 'Run `php configure.php --help` for usage.'.PHP_EOL);

    exit(1);
}

function printHelp(): void
{
    writeln(bold('Usage').': php configure.php [options]');
    writeln('');
    writeln(bold('Options').':');
    writeln('  --vendor=acme              Composer vendor, e.g. "acme" (required with --no-interaction)');
    writeln('  --name="Acme Widgets"      Human-readable package name');
    writeln('  --slug=acme-widgets        Composer package name (defaults to the directory name)');
    writeln('  --namespace="Acme\\Widgets" Root PHP namespace');
    writeln('  --author="Jane Doe"        Author name');
    writeln('  --email=jane@example.com   Author email');
    writeln('  --description="..."        Composer description');
    writeln('  --year=2026                Copyright year');
    writeln('  --no-interaction, -n       Never prompt; use flags and defaults');
    writeln('  --instructions             Print setup instructions (used by post-create-project-cmd)');
    writeln('  --keep-configure           Keep this script after configuring');
    writeln('  --force, -f                Allow configuring the template repository itself');
    writeln('  --help, -h                 Show this help');
}

function printInstructions(): void
{
    $slug = defaultPackageSlug(__DIR__);

    writeln(bold('Scaffold ready.'));
    writeln('');
    writeln('Configure it now:');
    writeln('');
    writeln('  php configure.php');
    writeln('');

    if ($slug === TEMPLATE_PACKAGE_SLUG) {
        writeln('This directory is named like the template, so configure needs an explicit slug:');
        writeln('');
        writeln('  php configure.php --vendor=acme --slug=acme-widgets');
    } else {
        writeln('Or pass everything up front. The slug defaults to "'.$slug.'", but the vendor does not:');
        writeln('');
        writeln('  php configure.php --vendor=acme --slug='.$slug.' --no-interaction');
    }

    writeln('');
    writeln('With --no-interaction, --vendor is required (and --slug when it cannot be derived).');
    writeln('Run `php configure.php --help` for every option.');
    writeln('');
    writeln('After configuring, regenerate the autoloader and run the gate:');
    writeln('');
    writeln('  composer install');
    writeln('  composer qa');
    writeln('');
}
