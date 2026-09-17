# Contributing

Thank you for considering contributing to the Filament CKEditor Field! This guide will help you set up a proper development environment and understand our contribution workflow.

## Table of Contents

- [Development Environment Setup](#development-environment-setup)
- [Local Development Approaches](#local-development-approaches)
- [Development Workflow](#development-workflow)
- [Testing](#testing)
- [Code Standards](#code-standards)
- [Commit Messages](#commit-messages)
- [Submitting Changes](#submitting-changes)
- [Releasing](#releasing)

## Development Environment Setup

### Prerequisites

- PHP 8.1 or higher. The test matrix runs 8.3 through 8.5.
- Composer 2.x
- Node.js 18 or higher, and npm. The floor comes from `esbuild`, which the
  asset build depends on.
- A Laravel application for testing (recommended: fresh Laravel + Filament installation)

### Quick Start

The fastest way to start contributing is to set up a local development environment using one of the methods below.

## Local Development Approaches

We recommend several approaches for local development, each with their own advantages:

### 🥇 Recommended: Composer Path Repository (Improved Method)

This is the most reliable and widely-used method for local PHP package development. Here's the improved version of your current approach:

1. **Set up the development environment:**
   ```bash
   # Directory structure should be:
   # ~/dev/
   # ├── filament-ckeditor-field/    (this package)
   # └── test-app/                   (Laravel app for testing)
   ```

2. **Configure composer.json in your test app:**
   ```json
   {
       "repositories": [
           {
               "type": "path",
               "url": "../filament-ckeditor-field",
               "options": {
                   "symlink": true
               }
           }
       ],
       "require": {
           "kahusoftware/filament-ckeditor-field": "@dev"
       }
   }
   ```

3. **Install with symlink preference:**
   ```bash
   composer update kahusoftware/filament-ckeditor-field --prefer-source
   ```

**Important:** Always use `"symlink": true` and `--prefer-source` to ensure changes are reflected immediately.

## Development Workflow

### 1. Fork and Clone

```bash
# Fork the repository on GitHub, then:
git clone https://github.com/YOUR-USERNAME/filament-ckeditor-field.git
cd filament-ckeditor-field
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Set Up Your Test Environment

Use one of the local development approaches above to create a test Laravel application.

### 4. Make Your Changes

- **PHP Code**: Located in `src/`
- **Frontend Assets**: Located in `resources/`
- **Configuration**: Located in `config/`
- **Views**: Located in `resources/views/`

### 5. Build Frontend Assets

When working with frontend assets:

```bash
# Development (watch for changes)
npm run dev

# Production build
npm run build
```

## Testing

The suite is [Pest](https://pestphp.com) running against a throwaway Laravel
application booted by [Orchestra Testbench](https://packages.tools/testbench),
so there is nothing to configure. `composer install` is the whole setup.

```bash
composer test              # the whole suite
composer test-coverage     # the same, with a coverage report
```

Both are thin wrappers around `vendor/bin/pest`, so anything Pest accepts works
directly:

```bash
vendor/bin/pest tests/Unit/EditorOptionsTest.php       # one file
vendor/bin/pest --filter="orphaned toolbar"            # one test, by description
vendor/bin/pest tests/Unit                             # one directory
vendor/bin/pest --bail                                 # stop at the first failure
```

Tests run in random order. When a failure only reproduces under one ordering,
re-run with the seed the failing run printed:

```bash
vendor/bin/pest --order-by=random --seed=1234567890
```

### How the suite is laid out

| Path | What lives there |
| --- | --- |
| `tests/Unit` | The option resolution, encoding, toolbar filtering and image-diff logic. Plain PHP, no rendering. |
| `tests/Feature` | Anything that has to go through Blade or Livewire: the rendered field, form integration, height and autofocus output. |
| `tests/ArchTest.php` | Architecture rules, such as no stray debug calls. |
| `tests/Helpers/Livewire.php` | Helpers for driving a Livewire component in a test. |
| `tests/views/test-form.blade.php` | The fixture form the feature tests render. |

If you are adding a test that asserts on what reaches the browser, read
`tests/Feature/HeightRenderingTest.php` first. It is the model for this
repository: render a field, then assert against the payload in the rendered
Blade output rather than against the DOM. Most of what this package does is
decide what to hand CKEditor, and that is the seam worth pinning.

### Writing Tests

- Place tests in the `tests/` directory
- Follow PSR-4 autoloading standards
- Use descriptive test method names
- Test both happy path and edge cases

### Manual Testing

Create a test form in your Laravel application:

```php
// In a Filament resource or form
use Kahusoftware\FilamentCkeditorField\CKEditor;

public static function form(Form $form): Form
{
    return $form->schema([
        CKEditor::make('content')
            ->uploadUrl('/upload-endpoint')
            ->label('Content'),
    ]);
}
```

## Code Standards

### PHP Standards

- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add proper DocBlocks for public methods
- Maintain backward compatibility when possible

## Commit Messages

**The [Conventional Commits](https://www.conventionalcommits.org) format is
required, not a style preference.** `release.yml` asks `git-cliff` for the next
version number, and `git-cliff` works it out from the commit subjects since the
last tag. A mistyped subject silently produces the wrong version.

```
<type>(<optional scope>)<optional !>: <subject>

<optional body>

<optional BREAKING CHANGE: footer>
```

### Types

`cliff.toml` is the source of truth. It parses exactly these ten:

| Type | Release notes | Effect on the version |
| --- | --- | --- |
| `feat` | **Added** | minor |
| `fix` | **Fixed** | patch |
| `perf` | **Changed** | patch |
| `refactor` | **Changed** | patch |
| `docs` | **Changed** | patch |
| `revert` | **Changed** | patch |
| `test` | *skipped* | none |
| `ci` | *skipped* | none |
| `build` | *skipped* | none |
| `chore` | *skipped* | none |

A `!` after the type or scope, or a `BREAKING CHANGE:` footer, moves the major
regardless of type. A commit whose **body** mentions security is grouped under
**Security** in the notes.

```
feat(editor): honour the lazy state binding modifier
fix: drop toolbar items whose plugin is not loaded
refactor!: remove the deprecated uploadUrl argument
chore(deps): bump esbuild
```

A subject that matches no type still appears in the notes, under **Changed**,
but it will not move the version the way you expect. Use a real type.

### A line carrying only skipped types has nothing to release

If every commit since the last tag is `test`, `ci`, `build` or `chore`, there is
no version to compute and `release.yml` errors out rather than guessing. That is
intended. Tooling-only work is not a release.

### The changelog is written by hand

Two things about `CHANGELOG.md` are easy to get wrong:

1. **Every user-facing PR writes its own entry**, under a `## [x.y.z]` heading
   for the version it will ship in. `release.yml` reads that section *verbatim*
   as the release notes and only falls back to generating them from commits when
   no matching section exists. What you write is what people read on the release
   page.
2. **The `(Discussion #NN)` references are a hand-written convention.**
   `cliff.toml` has no footer or trailer parser, so nothing extracts them from
   commit trailers. If you want the reference in the changelog, type it in the
   changelog.

### An optional local check

There is a `commit-msg` hook in `.githooks/` that checks the subject against
the table above before the commit is written. It is **opt-in**, and it is a
convenience rather than a gate:

```bash
git config core.hooksPath .githooks
```

That is per-clone. Hooks are not part of a repository's contents, so nothing
you do here affects anyone else, and a contributor who has not run that
command never sees the check. Nothing on a pull request enforces the format
today, so a wrong type will still reach the release workflow. The hook only
means you find out at commit time rather than at release time.

To turn it back off:

```bash
git config --unset core.hooksPath
```

The hook lets through the messages git writes for itself (merges, reverts,
`fixup!` and `squash!`), since those are rewritten or ignored before they
influence anything.

## Submitting Changes

### Before Submitting

**Test in a real application:**
   Ensure your changes work in an actual Laravel/Filament application.

### Pull Request Process

1. **Create a branch:**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes and commit:**
   ```bash
   git add .
   git commit -m "feat: add your feature description"
   ```

   The subject must follow [Commit Messages](#commit-messages) above. It
   decides the released version number.

3. **Push to your fork:**
   ```bash
   git push origin feature/your-feature-name
   ```

4. **Create a Pull Request:**
   - Provide a clear title and description
   - Reference any related issues
   - Include screenshots for any UI changes

### Pull Request Guidelines

- **One feature per PR** - Keep changes focused and atomic
- **Include tests** - Add tests for new functionality
- **Update documentation** - Update README.md if needed
- **Breaking changes** - Clearly document any breaking changes
- **Screenshots** - Include screenshots for visual changes

## Releasing

Releases are cut by the **Release** workflow in the Actions tab. Run it by
hand, against the release line you are shipping: pick `1.x` or `2.x` in the
branch selector. Nothing releases on a push, and the tag is what the workflow
makes rather than something you create first.

The version is worked out for you. `git-cliff` reads the commits since the last
tag *on that branch* and steps the number by the conventional-commit rules: a
`feat` moves the minor, a `fix` moves the patch, a breaking change moves the
major. This is why commit subjects are written the way they are.

Two inputs:

- **version** overrides the computed number. Reach for it when semver and the
  commit types disagree, which happens more than you would think: a release
  that adds a config option alongside a fix is a minor by semver but a patch by
  commit type.
- **dry_run** works out the version and prints the notes into the run summary
  without tagging, pushing or publishing. Worth doing first.

Release notes come from the matching `## [x.y.z]` section of `CHANGELOG.md`
when there is one, so the notes on the release page are the ones written by
hand. Only when no such section exists does the workflow generate them from the
commits. Write the changelog entry in the PR and the release page takes care of
itself.

The workflow will refuse to run if the branch is not `1.x` or `2.x`, if the
version does not match the line being released (a `v2` number on `1.x`), if the
tag already exists, or if the test suite fails on the commit being tagged. A
`1.x` release is published with `latest=false` so it does not displace the
newest `2.x` release on the repository front page.

## Community and Support

- **Discussions**: Issues are disabled on this repository. Bug reports, questions
  and ideas all go through [GitHub Discussions](https://github.com/Kahu-Software-LLC/filament-ckeditor-field/discussions).
- **Security**: Do not report vulnerabilities in public. See [SECURITY.md](.github/SECURITY.md).

## Development Resources

### Learning Resources

- [Laravel Package Development](https://laravel.com/docs/10.x/packages)
- [Spatie Package Tools](https://github.com/spatie/laravel-package-tools)
- [Filament Plugin Development](https://filamentphp.com/docs/3.x/support/plugins/getting-started)
- [CKEditor 5 Documentation](https://ckeditor.com/docs/ckeditor5/latest/)

## Questions?

If you have questions about contributing, feel free to:

- Open a [GitHub Discussion](https://github.com/Kahu-Software-LLC/filament-ckeditor-field/discussions)
- Email: [hello@kahusoftware.com](mailto:hello@kahusoftware.com)

Thank you for contributing to the Filament CKEditor Field! 🎉 
