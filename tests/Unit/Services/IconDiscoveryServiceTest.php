<?php

declare(strict_types=1);

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Matheusmarnt\TallIconPicker\Services\IconDiscoveryService;

beforeEach(function () {
    // Create a temporary directory structure for testing
    $this->testDir = __DIR__.'/../../temp_vendor';

    // Create Lucide structure
    $lucidePath = $this->testDir.'/mallardduck/blade-lucide-icons/resources/svg';
    if (! File::exists($lucidePath)) {
        File::makeDirectory($lucidePath, 0755, true);
    }
    File::put($lucidePath.'/home.svg', '<svg></svg>');
    File::put($lucidePath.'/user.svg', '<svg></svg>');

    // Create Heroicons structure
    $heroPath = $this->testDir.'/stijnvdk/blade-heroicons/resources/svg';
    if (! File::exists($heroPath)) {
        File::makeDirectory($heroPath, 0755, true);
    }
    File::put($heroPath.'/check.svg', '<svg></svg>');

    $this->basePath = base_path();
    $this->vendorPath = $this->basePath.'/vendor';

});

afterEach(function () {
    if (File::exists($this->testDir)) {
        File::deleteDirectory($this->testDir);
    }
});

it('can discover available libraries', function () {
    // Override config
    Config::set('tall-icon-picker.libraries', [
        'lucide' => [
            'package' => 'mallardduck/blade-lucide-icons',
            'path' => 'resources/svg',
            'label' => 'Lucide',
        ],
        'heroicons' => [
            'package' => 'stijnvdk/blade-heroicons',
            'path' => 'resources/svg',
            'label' => 'Heroicons',
        ],
    ]);

    $service = new IconDiscoveryService($this->testDir);
    $libraries = $service->getAvailableLibraries();

    expect($libraries)->toBeArray()
        ->and($libraries)->toHaveCount(2)
        ->and($libraries[0]['id'])->toBe('lucide')
        ->and($libraries[0]['name'])->toContain('(2)')
        ->and($libraries[1]['id'])->toBe('heroicons')
        ->and($libraries[1]['name'])->toContain('(1)');
});

it('can discover icons with pagination', function () {
    Config::set('tall-icon-picker.libraries', [
        'lucide' => [
            'package' => 'mallardduck/blade-lucide-icons',
            'path' => 'resources/svg',
            'label' => 'Lucide',
        ],
    ]);

    $service = new IconDiscoveryService($this->testDir);

    // Page 1, 1 per page
    $paginator = $service->discoverIcons(['lucide'], '', 1, 1);

    expect($paginator)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($paginator->total())->toBe(2)
        ->and($paginator->items())->toHaveCount(1)
        ->and($paginator->items()[0])->toBe('lucide-home'); // alphabetical order: home, user

    // Page 2
    $paginator = $service->discoverIcons(['lucide'], '', 2, 1);
    expect($paginator->items()[0] ?? $paginator->items()[1])->toBe('lucide-user');
});

it('can search icons', function () {
    Config::set('tall-icon-picker.libraries', [
        'lucide' => [
            'package' => 'mallardduck/blade-lucide-icons',
            'path' => 'resources/svg',
            'label' => 'Lucide',
        ],
    ]);

    $service = new IconDiscoveryService($this->testDir);
    $paginator = $service->discoverIcons(['lucide'], 'user', 1, 10);

    expect($paginator->total())->toBe(1)
        ->and($paginator->items()[0])->toBe('lucide-user');
});

it('silently ignores a library not present in config', function () {
    Config::set('tall-icon-picker.libraries', [
        'lucide' => [
            'package' => 'mallardduck/blade-lucide-icons',
            'path' => 'resources/svg',
            'label' => 'Lucide',
        ],
    ]);

    $service = new IconDiscoveryService($this->testDir);
    // 'heroicons' key is not in config — should return empty paginator
    $paginator = $service->discoverIcons(['heroicons'], '', 1, 10);

    expect($paginator->total())->toBe(0)
        ->and($paginator->items())->toBeEmpty();
});

it('searches icons case-insensitively', function () {
    Config::set('tall-icon-picker.libraries', [
        'lucide' => [
            'package' => 'mallardduck/blade-lucide-icons',
            'path' => 'resources/svg',
            'label' => 'Lucide',
        ],
    ]);

    $service = new IconDiscoveryService($this->testDir);
    $paginator = $service->discoverIcons(['lucide'], 'HOME', 1, 10);

    expect($paginator->total())->toBe(1)
        ->and($paginator->items()[0])->toBe('lucide-home');
});
