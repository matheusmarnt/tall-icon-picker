<?php

declare(strict_types=1);

namespace Matheusmarnt\TallIconPicker\Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use Illuminate\Support\Facades\File;
use Livewire\LivewireServiceProvider;
use Matheusmarnt\TallIconPicker\TallIconPickerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            BladeIconsServiceProvider::class,
            TallIconPickerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('view.paths', [__DIR__.'/../resources/views']);

        // Register dummy components for TallStackUI
        $app['blade.compiler']->component('tall-icon-picker-test::components.dummy', 'ts-slide');
        $app['blade.compiler']->component('tall-icon-picker-test::components.dummy', 'ts-button');
        $app['blade.compiler']->component('tall-icon-picker-test::components.dummy', 'ts-select.styled');
        $app['blade.compiler']->component('tall-icon-picker-test::components.dummy', 'ts-input');

        // Register dummy icon for testing
        $app['blade.compiler']->component('tall-icon-picker-test::components.dummy', 'lucide-home');

        // We can add some fake library config for testing
        $app['config']->set('tall-icon-picker.libraries', [
            'lucide' => [
                'package' => 'mallardduck/blade-lucide-icons',
                'path' => 'resources/svg',
                'label' => 'Lucide',
            ],
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create a dummy view for the components
        $this->app['view']->addNamespace('tall-icon-picker-test', __DIR__.'/views');
        File::ensureDirectoryExists(__DIR__.'/views/components');
        File::put(__DIR__.'/views/components/dummy.blade.php', '<div>{{ $slot ?? "" }}</div>');
    }
}
