# Filament CKEditor Field

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kahusoftware/filament-ckeditor-field.svg?style=flat-square)](https://packagist.org/packages/kahusoftware/filament-ckeditor-field)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kahu-software-llc/filament-ckeditor-field/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kahu-software-llc/filament-ckeditor-field/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kahusoftware/filament-ckeditor-field.svg?style=flat-square)](https://packagist.org/packages/kahusoftware/filament-ckeditor-field)
[![License](https://img.shields.io/packagist/l/kahusoftware/filament-ckeditor-field.svg?style=flat-square)](LICENSE.md)

[![Plumb score](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/composite.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)
[![Plumb security score](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/security.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)
[![Plumb maintenance score](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/maintenance.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)
[![Plumb ecosystem score](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/ecosystem.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)
[![Scanned by Plumb](https://plumbphp.dev/badges/kahusoftware/filament-ckeditor-field/scanned.svg)](https://plumbphp.dev/kahusoftware/filament-ckeditor-field)

> **Note:** This branch (`1.x`) is specifically for FilamentPHP 3.x. If you're using FilamentPHP 4.x, please use the [`2.x` branch](https://github.com/kahu-software-llc/filament-ckeditor-field/tree/2.x).

![](https://cdn.kahusoftware.com/uploads/kahu-software-llc-ckeditor-field.jpg)

## Features

-   CKEditor 5 integration for FilamentPHP 3 forms
-   Image upload support with configurable upload URLs
-   Full control over image upload handling - you implement your own upload endpoint
-   Highly customizable with fluent API
-   Non-premium features only (free and open-source)
-   Easy to configure and use

<br>

## Table of contents

- [Filament CKEditor Field](#filament-ckeditor-field)
- [Features](#features)
- [Table of contents](#table-of-contents)
- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
  - [Available methods](#available-methods)
    - [uploadUrl(`string` | `Closure` | `null` $uploadUrl)](#uploadurlstring--closure--null-uploadurl)
    - [name(`string` $name)](#namestring-name)
    - [placeholder(`string` $placeholder)](#placeholderstring-placeholder)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)

<br>

## Installation

You can install the field via composer:

```bash
composer require kahusoftware/filament-ckeditor-field
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-ckeditor-field-config"
```

<br>

## Usage

Basic usage:

```php
use Kahusoftware\FilamentCkeditorField\CKEditor;

CKEditor::make('content')
    ->uploadUrl(null)
```

<br>

## Configuration

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
];
```

### Available methods

#### uploadUrl(`string` | `Closure` | `null` $uploadUrl)
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

#### name(`string` $name)
Sets the name of the field. This will be used as the form field name.

`name` (Default: `'ckeditor'`)

#### placeholder(`string` $placeholder)
Sets the placeholder text displayed in the editor when it's empty.

`placeholder` (Default: `'Type or paste your content here...'`)

<br>

## Testing

```bash
composer test
```

The test suite uses PestPHP and includes unit tests for field instantiation, method chaining, and configuration, as well as feature tests for rendering the field within Livewire components.

<br>

## Changelog

Please see [CHANGELOG](https://github.com/Kahu-Software-LLC/filament-ckeditor-field/blob/1.x/CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/Kahu-Software-LLC/filament-ckeditor-field/blob/1.x/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please email [hello@kahusoftware.com](mailto:hello@kahusoftware.com) any security vulnerabilities to ensure they're promptly addressed.

## Credits

-   [Thomas Johnson](https://github.com/tominal)
-   [All Contributors](https://github.com/Kahu-Software-LLC/filament-ckeditor-field/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/Kahu-Software-LLC/filament-ckeditor-field/blob/1.x/LICENSE.md) for more information.

---

&ast; *This open-source plugin is not affiliated with, endorsed, or sponsored by CKSource, and any references to CKEditor are solely for descriptive purposes under their respective copyrights and trademarks.*

We do encourage you to check out CKEditor's premium features for your own implementation of CKEditor as the developers have worked hard to bring us a wonderful rich editor.
