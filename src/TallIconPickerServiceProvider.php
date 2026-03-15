<?php

declare(strict_types=1);

namespace Matheusmarnt\TallIconPicker;

use Illuminate\Support\Facades\Config;
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

        $this->app->singleton(IconDiscoveryService::class, fn () => new IconDiscoveryService(base_path('vendor')));
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tall-icon-picker');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tall');

        $this->app->booted(function (): void {
            // 'tall.icon-picker'  — Livewire v4 (dot notation, no :: support)
            // 'tall::icon-picker' — Livewire v3 backward compatibility
            Livewire::component('tall.icon-picker', IconPicker::class);
            Livewire::component('tall::icon-picker', IconPicker::class);
        });

        Config::set('tall-icon-picker.ui', $this->resolveUiAdapter());

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tall-icon-picker.php' => config_path('tall-icon-picker.php'),
            ], 'tall-icon-picker-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/tall'),
            ], 'tall-icon-picker-views');

            $this->publishes([
                __DIR__.'/../resources/lang' => lang_path('vendor/tall-icon-picker'),
            ], 'tall-icon-picker-translations');
        }
    }

    private function resolveUiAdapter(): string
    {
        $configured = Config::get('tall-icon-picker.ui', 'auto');

        if ($configured !== 'auto') {
            return $configured;
        }

        return class_exists(\TallStackUI\TallStackUIServiceProvider::class)
            ? 'tallstackui'
            : 'native';
    }
}
