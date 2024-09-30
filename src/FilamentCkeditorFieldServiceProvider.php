<?php

namespace Kahusoftware\FilamentCkeditorField;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__ . '/../resources/dist/filament-ckeditor-field.css' => public_path('vendor/kahusoftware/filament-ckeditor-field/filament-ckeditor-field.css'),
            __DIR__ . '/../resources/dist/filament-ckeditor-field.js' => public_path('vendor/kahusoftware/filament-ckeditor-field/filament-ckeditor-field.js'),
        ], 'filament-ckeditor-field');
    }
 
    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasViews();
    }
 
    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('filament-ckeditor-field', __DIR__ . '/../resources/dist/filament-ckeditor-field.css'),
            Js::make('filament-ckeditor-field', __DIR__ . '/../resources/dist/filament-ckeditor-field.js'),
        ], 'kahusoftware/filament-ckeditor-field');

        // Register the render hook to inject the script into the head
        FilamentView::registerRenderHook(
            'panels::head.end',
            function (): string {
                return <<<'HTML'
                    <script>
                        window.ckeditorInstances = window.ckeditorInstances || {};
                    </script>
                HTML;
            }
        );
    }
}
