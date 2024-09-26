<?php

namespace Kahusoftware\FilamentCkeditorField;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use kahusoftware\FilamentCkeditorField\Commands\FilamentCkeditorFieldCommand;
use kahusoftware\FilamentCkeditorField\Testing\TestsFilamentCkeditorField;

class FilamentCkeditorFieldServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-ckeditor-field';
 
    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews();
    }
 
    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('filament-ckeditor-field', __DIR__ . '/../resources/dist/filament-ckeditor-field.css')->loadedOnRequest(),
        ], 'filament-ckeditor-field');
    }
}
