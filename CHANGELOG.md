# Changelog

All notable changes to `filament-ckeditor-field` will be documented in this file.

## [Unreleased]

### Fixed
- The editor now initialises for fields added to the page after the initial load, such as repeater items and other Livewire-morphed DOM. Editor setup no longer relies on inline `<script>` tags, which never execute in morphed HTML. (Discussion #54)
- The field now works outside Filament panels. Setup previously depended on a `panels::head.end` render hook that standalone Livewire pages never render. (Discussion #53)
- Tearing the field down while editor creation is in flight (fast SPA navigation) no longer leaks the editor or leaves a stale instance behind, and editor chrome restored from a navigation snapshot is removed before re-initialisation. (Discussions #55, #45)

### Changed
- The view was rewritten around Filament's asynchronous Alpine component loader (`x-load`), replacing the inline-script and `window.ckeditorInstances` registry architecture.

## [1.1.2] - 2026-08-03

### Fixed
- Resetting the field state to `null` (e.g. `$this->form->fill()` after a submit action) now clears the editor. The state watcher previously skipped `null` and `undefined`, leaving stale content in the editor after a form reset.

## [1.1.1] - 2026-08-02

### Fixed
- A published config declaring a partial `editor` key no longer wipes the plugin list and toolbar. The application's `editor` config is now deep-merged over the package defaults, so any omitted key falls back instead of vanishing.

### Changed
- Config stub and README now link the CKEditor references for editor options, toolbar item names and per-plugin feature docs, and document the upgrade path for configs published before 1.1.0.

## [1.1.0] - 2026-08-02

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
## [1.0.6] - 2026-08-02

Maintenance release. No package source changes.

### Changed
- CI test matrix now covers PHP 8.3/8.4/8.5 and Laravel 12/13; end-of-life PHP and Laravel versions are no longer tested
- Forgejo CI runs on runner-native PHP with no third-party setup actions
- Development dependencies modernized (Testbench 10/11, Pest 3/4)

### Fixed
- Bumped dev-only `shell-quote` to 1.10.0 (GHSA-395f-4hp3-45gv, GHSA-w7jw-789q-3m8p)

## [1.0.5] - 2026-08-02

Maintenance release focused on CI and supply-chain hardening. No package source changes.

### Security
- GitHub Actions pinned to full commit SHAs with the workflow token restricted to read access
- Dependency updates handled by Renovate with a 7-day release cooldown; Dependabot version updates retired

### Fixed
- Repaired the test workflow and Livewire form integration tests (#4)

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
