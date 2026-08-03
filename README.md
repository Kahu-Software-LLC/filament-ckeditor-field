# Filament CKEditor Field

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kahusoftware/filament-ckeditor-field.svg?style=flat-square)](https://packagist.org/packages/kahusoftware/filament-ckeditor-field)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kahu-software-llc/filament-ckeditor-field/run-tests.yml?branch=2.x&label=tests&style=flat-square)](https://github.com/kahu-software-llc/filament-ckeditor-field/actions?query=workflow%3Arun-tests+branch%3A2.x)
[![Total Downloads](https://img.shields.io/packagist/dt/kahusoftware/filament-ckeditor-field.svg?style=flat-square)](https://packagist.org/packages/kahusoftware/filament-ckeditor-field)
[![License](https://img.shields.io/packagist/l/kahusoftware/filament-ckeditor-field.svg?style=flat-square)](LICENSE.md)

[![Plumb score](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/composite.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)
[![Plumb security score](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/security.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)
[![Plumb maintenance score](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/maintenance.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)
[![Plumb ecosystem score](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/ecosystem.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)
[![Scanned by Plumb](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/scanned.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)

> **Note:** This branch (`2.x`) is specifically for FilamentPHP 4.x. If you're using FilamentPHP 3.x, please use the [`1.x` branch](https://github.com/kahu-software-llc/filament-ckeditor-field/tree/1.x).

![](https://cdn.kahusoftware.com/uploads/kahu-software-llc-ckeditor-field.jpg)

# Features

-   CKEditor 5 integration for FilamentPHP 4 forms
-   Image upload support with configurable upload URLs
-   Full control over image upload handling - you implement your own upload endpoint
-   Full control over the editor configuration from config, a service provider, or per field
-   Highly customizable with fluent API
-   Non-premium features only (free and open-source)
-   Easy to configure and use

<br>

# Table of contents

- [Filament CKEditor Field](#filament-ckeditor-field)
- [Features](#features)
- [Table of contents](#table-of-contents)
- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
  - [Editor options](#editor-options)
    - [Upgrading from 2.0.x](#upgrading-from-20x)
    - [Where to set them](#where-to-set-them)
    - [How options merge](#how-options-merge)
    - [Plugins and toolbar items](#plugins-and-toolbar-items)
    - [JavaScript expressions](#javascript-expressions)
  - [Available methods](#available-methods)
    - [uploadUrl(`string` | `Closure` | `null` $uploadUrl)](#uploadurlstring--closure--null-uploadurl)
    - [name(`string` $name)](#namestring-name)
    - [placeholder(`string` $placeholder)](#placeholderstring-placeholder)
    - [height(`string` | `Closure` | `null` $height)](#heightstring--closure--null-height)
    - [minHeight(`string` | `Closure` | `null` $minHeight)](#minheightstring--closure--null-minheight)
    - [editorOptions(`array` | `Closure` $options)](#editoroptionsarray--closure-options)
    - [disablePlugins(`array` | `Closure` $plugins)](#disablepluginsarray--closure-plugins)
    - [enablePlugins(`array` | `Closure` $plugins)](#enablepluginsarray--closure-plugins)
    - [disableToolbarItems(`array` | `Closure` $items)](#disabletoolbaritemsarray--closure-items)
  - [Inherited field methods](#inherited-field-methods)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)

<br>

# Installation

You can install the field via composer:

```bash
composer require kahusoftware/filament-ckeditor-field
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-ckeditor-field-config"
```

<br>

# Usage

Basic usage:

```php
use Kahusoftware\FilamentCkeditorField\CKEditor;

CKEditor::make('content')
    ->uploadUrl(null)
```

<br>

# Configuration

This is the contents of the published config file:

```php
return [
    /**
     * Image upload enabled
     */
    'upload_enabled' => true,

    /**
     * Image URL to upload to if one is not specified on the form field's ->uploadUrl() method
     */
    'upload_url' => null,

    /**
     * Everything the CKEditor instance is built from: plugins, toolbar,
     * htmlSupport, headings, styles and so on. See "Editor options" below.
     */
    'editor' => [
        'plugins' => [/* ... */],
        'upload_only_plugins' => ['ImageInsert', 'ImageUpload', 'SimpleUploadAdapter'],
        'upload_only_toolbar_items' => ['insertImage'],
        'disabled_plugins' => [],
        'disabled_toolbar_items' => [],
        'options' => [/* ... */],
    ],
];
```

<br>

## Editor options

The full CKEditor configuration lives under the `editor` key of the config file.
Publish the config to see and edit the complete defaults.

Any key the published config omits falls back to the package default, so a
partial `editor` block is always safe: declaring only `disabled_plugins`, for
example, leaves the default plugin list, toolbar and option groups intact.

Useful references while editing:

- [Editor options under `options`](https://ckeditor.com/docs/ckeditor5/latest/api/module_core_editor_editorconfig-EditorConfig.html)
- [Toolbar item names](https://ckeditor.com/docs/ckeditor5/latest/getting-started/setup/toolbar.html)
- [Feature documentation per plugin](https://ckeditor.com/docs/ckeditor5/latest/features/index.html)

### Upgrading from 2.0.x

A config file published before 2.1.0 has no `editor` key and keeps working
unchanged: the package defaults apply, and the editor behaves exactly as it did
before the configuration was extracted. To see and edit the full defaults,
republish the config:

```bash
php artisan vendor:publish --tag="filament-ckeditor-field-config" --force
```

`--force` overwrites the existing file, so re-apply any `upload_enabled` or
`upload_url` customizations afterwards. Alternatively, add just the `editor`
keys you want to change; everything omitted falls back to the defaults.

### Where to set them

Options resolve in four layers, and later layers win: the package defaults,
then the published config file, then `CKEditor::configureUsing()`, then the
methods on an individual field.

**1. Application wide, in the published config file.**

```php
// config/filament-ckeditor-field.php
'editor' => [
    'disabled_plugins' => ['FontColor', 'FontBackgroundColor', 'Highlight'],
    'disabled_toolbar_items' => ['fontColor', 'fontBackgroundColor', 'highlight'],
],
```

**2. Application wide, in a service provider.**

```php
use Kahusoftware\FilamentCkeditorField\CKEditor;

public function boot(): void
{
    CKEditor::configureUsing(fn (CKEditor $field) => $field
        ->disablePlugins(['FontColor', 'FontBackgroundColor', 'Highlight'])
        ->disableToolbarItems(['fontColor', 'fontBackgroundColor', 'highlight']));
}
```

**3. On a single field.**

```php
CKEditor::make('content')
    ->editorOptions([
        'menuBar' => ['isVisible' => false],
    ])
    ->disableToolbarItems(['insertTable'])
```

### How options merge

String-keyed arrays merge recursively, while list-shaped arrays are replaced
outright. Overriding a list therefore swaps it wholesale rather than appending
to it, and passing an empty array clears it.

```php
// Replaces the seven default sizes with two, rather than adding to them.
->editorOptions(['fontSize' => ['options' => [12, 16]]])

// Leaves link.addTargetToExternalLinks untouched.
->editorOptions(['link' => ['defaultProtocol' => 'http://']])
```

### Plugins and toolbar items

Plugins are named as strings and resolved against the JavaScript `window` scope
at runtime. Names that are not bundled are skipped, so removing plugins is
always safe while adding one requires it to be present in the bundle.

Removing a toolbar item hides its button. Removing a **plugin** switches the
feature off entirely, which also hands whatever markup it owned back to
[General HTML Support](https://ckeditor.com/docs/ckeditor5/latest/features/html/general-html-support.html).
That distinction matters: an `htmlSupport.disallow` rule has no effect while the
plugin that owns the markup is still active, because the plugin's own converters
handle it first. To strip markup rather than merely hide a button, disable the
plugin *and* disallow the markup.

```php
CKEditor::make('content')
    ->disablePlugins(['FontColor', 'FontBackgroundColor', 'Highlight'])
    ->disableToolbarItems(['fontColor', 'fontBackgroundColor', 'highlight'])
    ->editorOptions([
        'htmlSupport' => [
            'allow' => [[
                'name' => 'js:/^.*$/',
                'classes' => true,
                'attributes' => true,
                // An explicit allowlist, rather than `true` for every style.
                'styles' => ['text-align', 'font-size', 'font-family'],
            ]],
            'disallow' => [
                ['name' => 'js:/^(font|mark)$/'],
                ['attributes' => ['bgcolor', 'color']],
                ['styles' => ['color', 'background', 'background-color']],
            ],
        ],
    ])
```

Separators (`|`) left with nothing to divide are dropped automatically, so
removing items never leaves stray dividers in the toolbar.

### JavaScript expressions

Some CKEditor options expect values that JSON cannot express, such as the
regular expression in `htmlSupport`. Prefix a string with `js:` and it is written
into the page as a bare JavaScript expression instead of a quoted string:

```php
'name' => 'js:/^.*$/',
```

At runtime you can also pass a `Filament\Support\RawJs` instance. In the config
file use the `js:` string form, because objects do not survive
`php artisan config:cache`.

> **Note**
> A `js:` value is emitted verbatim. Only use it for values you control, never
> for user input.

<br>

## Available methods

### uploadUrl(`string` | `Closure` | `null` $uploadUrl)
Sets the URL endpoint for image uploads. If not specified, the default upload URL from the config file will be used.

`uploadUrl` (Default: `null`)
> **Note:** This field gives you freedom to handle image uploads yourself. You are responsible for creating your own upload endpoint that handles file validation, storage, and returns the appropriate response format. This design allows you to implement your own business logic, security measures, and storage solutions (local filesystem, S3, cloud storage, etc.).

This field uses CKEditor's [Custom Upload Adapter](https://ckeditor.com/docs/ckeditor5/latest/framework/deep-dive/upload-adapter.html), which requires your upload endpoint to return a JSON response containing the uploaded image URL(s).

**Expected Response Format:**

Your upload endpoint must return a JSON response with one of the following formats:

**Single image response:**
```json
{
    "url": "https://example.com/uploads/image.jpg"
}
```

**Responsive images response:**
```json
{
    "urls": {
        "default": "https://example.com/uploads/image.jpg",
        "500": "https://example.com/uploads/image1.jpg",
        "1000": "https://example.com/uploads/image2.jpg"
    }
}
```

**Example Laravel Controller:**

```php
use Illuminate\Http\Request;

public function uploadImage(Request $request)
{
    $request->validate([
        'upload' => 'required|image|max:2048',
    ]);

    $path = $request->file('upload')->store('uploads', 'public');
    $url = asset('storage/' . $path);

    return response()->json([
        'url' => $url
    ]);
}
```

For more details, see the [CKEditor Custom Upload Adapter documentation](https://ckeditor.com/docs/ckeditor5/latest/framework/deep-dive/upload-adapter.html#passing-additional-data-to-the-response).

### name(`string` $name)
Sets the name of the field. This will be used as the form field name.

`name` (Default: `'ckeditor'`)

### placeholder(`string` $placeholder)
Sets the placeholder text displayed in the editor when it's empty.

`placeholder` (Default: `'Type or paste your content here...'`)

### height(`string` | `Closure` | `null` $height)
Fixes the editing area to the given CSS height, scrolling internally once
content outgrows it. Without it the editor grows with its content.

```php
CKEditor::make('content')
    ->height('400px')
```

### minHeight(`string` | `Closure` | `null` $minHeight)
Lets the editing area start at the given CSS height while still growing with
its content.

```php
CKEditor::make('content')
    ->minHeight('10rem')
```

### editorOptions(`array` | `Closure` $options)
Merges options over the resolved editor configuration for this field. See
[How options merge](#how-options-merge). Can be called more than once, with later
calls taking precedence.

```php
CKEditor::make('content')
    ->editorOptions(['menuBar' => ['isVisible' => false]])
```

### disablePlugins(`array` | `Closure` $plugins)
Removes plugins from the resolved plugin list, switching those features off.

```php
CKEditor::make('content')
    ->disablePlugins(['FontColor', 'FontBackgroundColor', 'Highlight'])
```

### enablePlugins(`array` | `Closure` $plugins)
Re-enables plugins that the config file or a `configureUsing` callback disabled.

```php
CKEditor::make('content')
    ->enablePlugins(['Highlight'])
```

### disableToolbarItems(`array` | `Closure` $items)
Removes items from the toolbar, leaving the underlying plugins active. Orphaned
separators are dropped automatically.

```php
CKEditor::make('content')
    ->disableToolbarItems(['insertTable', 'codeBlock'])
```

<br>

## Inherited field methods

The field extends Filament's base `Field`, so everything a standard form field
supports works here without package code, including:

```php
CKEditor::make('content')
    ->label('Body')
    ->autofocus()          // focuses the editor once it has initialised
    ->required()
    ->disabled()
    ->hidden()
    ->helperText('Shown under the field')
    ->columnSpanFull()
```

See the [Filament form field documentation](https://filamentphp.com/docs/forms/fields/getting-started)
for the full list.

# Testing

```bash
composer test
```

The test suite uses PestPHP and includes unit tests for field instantiation, method chaining, and configuration, as well as feature tests for rendering the field within Livewire components.

<br>

# Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

# Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

# Security Vulnerabilities

Please email [hello@kahusoftware.com](mailto:hello@kahusoftware.com) any security vulnerabilities to ensure they're promptly addressed.

# Credits

-   [Thomas Johnson](https://github.com/tominal)
-   [All Contributors](../../contributors)

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

&ast; *This open-source plugin is not affiliated with, endorsed, or sponsored by CKSource, and any references to CKEditor are solely for descriptive purposes under their respective copyrights and trademarks.*

We do encourage you to check out CKEditor's premium features for your own implementation of CKEditor as the developers have worked hard to bring us a wonderful rich editor.
