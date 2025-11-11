<?php

namespace Kahusoftware\FilamentCkeditorField\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Kahusoftware\FilamentCkeditorField\FilamentCkeditorFieldServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fix for Laravel 11 compatibility: Ensure ViewErrorBag is properly initialized
        // This prevents Livewire from trying to put null MessageBag instances
        // Share errors globally so Livewire always has access to properly initialized error bags
        $errorBag = new ViewErrorBag();
        $errorBag->put('default', new MessageBag());
        view()->share('errors', $errorBag);

        // Use a view composer to ensure errors are always properly initialized before rendering
        // This catches cases where Livewire creates new ViewErrorBag instances
        view()->composer('*', function ($view) {
            if (!isset($view->errors) || !($view->errors instanceof ViewErrorBag)) {
                $errorBag = new ViewErrorBag();
                $errorBag->put('default', new MessageBag());
                $view->with('errors', $errorBag);
            } elseif (!$view->errors->hasBag('default')) {
                $view->errors->put('default', new MessageBag());
            }
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            // FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            // TablesServiceProvider::class,
            // WidgetsServiceProvider::class,
            FilamentCkeditorFieldServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        
        // Set up app key for encryption (required for Livewire)
        config()->set('app.key', 'base64:' . base64_encode(
            \Illuminate\Support\Str::random(32)
        ));

        // Set up views path for test views
        $app['view']->addNamespace('test', __DIR__ . '/views');

        // Fix for Laravel 11 compatibility: Ensure ViewErrorBag is properly initialized
        // at the application level to prevent Livewire from trying to put null MessageBag instances
        $app->resolving('view', function ($view) {
            $errorBag = new ViewErrorBag();
            $errorBag->put('default', new MessageBag());
            $view->share('errors', $errorBag);
        });
    }
}
