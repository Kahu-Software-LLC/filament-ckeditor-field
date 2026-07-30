# Changelog

All notable changes to `filament-ckeditor-field` will be documented in this file.

## [Unreleased]

### Added
- Configurable editor options. The full CKEditor configuration now lives under the `editor` key of the config file instead of being hard-coded in the Blade view, and can be overridden application wide via the published config, globally via `CKEditor::configureUsing()`, or per field via `->editorOptions()`.
- `->editorOptions()`, `->disablePlugins()`, `->enablePlugins()` and `->disableToolbarItems()` field methods.
- `js:` string prefix for option values that must be emitted as bare JavaScript expressions, such as the regular expression in `htmlSupport`. `Filament\Support\RawJs` instances are also accepted at runtime.

### Fixed
- `CKEditor::configureUsing()` callbacks were never applied, because the field's `make()` override did not call Filament's `configure()`. It now does.
- Options are serialised with JSON escaping for a script context, replacing direct interpolation of the placeholder into a JavaScript string literal.
- Toolbar separators left with nothing to divide are now dropped instead of rendering as empty dividers.

### Removed
- The unreachable `->dehydrated(false)` call in `setUp()`. `make()` never invoked `configure()`, so `setUp()` never ran and the field has always been dehydrated. Now that `configure()` is called, keeping the line would have stopped editor content from being saved. A regression test pins the dehydrated behaviour.

### Notes
- Defaults are unchanged: the resolved configuration matches what the previous Blade view produced, asserted by parity tests, so upgrading does not alter any editor's behaviour.

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
