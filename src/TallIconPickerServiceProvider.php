<?php

namespace Matheusmarnt\TallIconPicker;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Matheusmarnt\TallIconPicker\Livewire\IconPicker;
use Matheusmarnt\TallIconPicker\Services\IconDiscoveryService;

class TallIconPickerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tall-icon-picker.php', 'tall-icon-picker'
        );

        $this->app->singleton(IconDiscoveryService::class, fn () => new IconDiscoveryService());
    }

    public function boot(): void
    {
        // 1. Register the views namespace as 'tall' only
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tall');

        // 2. Register the Livewire component with the alias 'tall::icon-picker'
        Livewire::component('tall::icon-picker', IconPicker::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tall-icon-picker.php' => config_path('tall-icon-picker.php'),
            ], 'tall-icon-picker-config');

            // 3. Update the publish path to reflect the new namespace.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/tall'),
            ], 'tall-icon-picker-views');
        }
    }
}