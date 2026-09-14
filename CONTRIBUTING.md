# Contributing

Thank you for considering contributing to the Filament CKEditor Field! This guide will help you set up a proper development environment and understand our contribution workflow.

## Table of Contents

- [Development Environment Setup](#development-environment-setup)
- [Local Development Approaches](#local-development-approaches)
- [Development Workflow](#development-workflow)
- [Testing](#testing)
- [Frontend Development](#frontend-development)
- [Code Standards](#code-standards)
- [Submitting Changes](#submitting-changes)
- [Releasing](#releasing)

## Development Environment Setup

### Prerequisites

- PHP 8.1 or higher
- Composer 2.x
- Node.js 16+ and npm
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

todo

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

- **Issues**: Use GitHub Issues for bug reports and feature requests
- **Discussions**: Use GitHub Discussions for questions and ideas

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
