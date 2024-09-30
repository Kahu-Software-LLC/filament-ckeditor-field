# A basic CKEditor 5 form field configured with non-premium features.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kahu-software-llc/filament-ckeditor-field.svg?style=flat-square)](https://packagist.org/packages/kahu-software-llc/filament-ckeditor-field)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kahu-software-llc/filament-ckeditor-field/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kahu-software-llc/filament-ckeditor-field/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kahu-software-llc/filament-ckeditor-field/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kahu-software-llc/filament-ckeditor-field/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kahu-software-llc/filament-ckeditor-field.svg?style=flat-square)](https://packagist.org/packages/kahu-software-llc/filament-ckeditor-field)



This repository enables FilamentPHP forms to use CKEditor 5 and its many free features without much configuration.

## Installation

You can install the package via composer:

```bash
composer require kahusoftware/filament-ckeditor-field
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-ckeditor-field-config"
```
<!--

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-ckeditor-field-views"
```

-->
This is the contents of the published config file:

```php
return [
    /**
     * Image upload enabled
     * 
     * WARNING: Setting this to false will use CKEditor's default Base64 upload method which is HIGHLY INEFFICIENT.
     * https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html#base64-adapter
     */
    'upload_enabled' => true,

    /**
     * Image URL to upload to if one is not specified on the form field's ->uploadUrl() method
     */
    'upload_url' => null,
];
```

## Usage

```php
use Kahusoftware\FilamentCkeditorField\CKEditor;

CKEditor::make('content')
    ->uploadUrl(null)
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Thomas Johnson](https://github.com/tominal)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
