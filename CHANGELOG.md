# Changelog

All notable changes to `filament-ckeditor-field` will be documented in this file.

## [2.0.2] - 2026-08-02

First stable release of the 2.x line. FilamentPHP 4 is now officially supported.

### Changed
- `filament/forms` constraint narrowed to `^4.0`. Filament 3 applications should use the 1.x line, which Composer will resolve automatically.
- PHP requirement raised to `^8.3`, matching the tested, security-viable matrix
- CI test matrix now covers PHP 8.3/8.4/8.5 and Laravel 12/13; end-of-life PHP and Laravel versions are no longer tested
- Forgejo CI runs on runner-native PHP with no third-party setup actions
- Development dependencies modernized (Testbench 10/11, Pest 3/4)

### Security
- GitHub Actions pinned to full commit SHAs with the workflow token restricted to read access
- Dependency updates handled by Renovate with a 7-day release cooldown; Dependabot version updates retired

### Fixed
- Bumped dev-only `shell-quote` to 1.10.0 (GHSA-395f-4hp3-45gv, GHSA-w7jw-789q-3m8p)

## [0.1.0-alpha] - 2024-XX-XX

### Added
- Confirmed both Spatie and Solutions Forest's translatable plugins function

## [0.0.7-alpha] - 2024-XX-XX

### Fixed
- Fixed a 3.0 installation error
- Fixed multiple CKEditors on a single resource

### Changed
- Removed the URL from the name variable
- Removed image upload if no URL is specified; Linking images by URL is still possible via Insert -> Image

### Added
- Added disabled behavior for default infolists and disabled attributes

## [0.0.6-alpha] - 2024-09-16

- Merge branch 'develop'

## [0.0.4-alpha] - 2024-01-07

### Fixed
- Fix for dark mode (#4)
- Fix for required field not displaying error (#6)

## [0.0.1-alpha] - 2024-09-30

### Added
- Initial Release

**Note:** v0.0.1-alpha should be clear enough that this release is infrequently tested in internal projects. This should not be considered ready for production.

The basic MVP includes:
- CKEditor functions in SPA mode
- CKEditor functions in non-SPA mode
- Images dragged/dropped use CKEditor's simple upload adapter to POST images to a developer's desired route
- An X padding update to test custom styling from the plugin
