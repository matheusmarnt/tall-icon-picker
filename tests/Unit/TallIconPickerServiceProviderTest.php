<?php

declare(strict_types=1);

use Matheusmarnt\TallIconPicker\Livewire\IconPicker;
use Matheusmarnt\TallIconPicker\TallIconPickerServiceProvider;

function resolveAdapter(string $uiConfig): string
{
    config()->set('tall-icon-picker.ui', $uiConfig);
    $provider = new TallIconPickerServiceProvider(app());
    $method = new ReflectionMethod($provider, 'resolveUiAdapter');
    $method->setAccessible(true);

    return $method->invoke($provider);
}

it('registers the livewire component under dot notation', function () {
    expect(app('livewire')->getClass('tall.icon-picker'))->toBe(IconPicker::class);
});

it('registers the livewire component under double-colon notation for Livewire v3 compatibility', function () {
    expect(app('livewire')->getClass('tall::icon-picker'))->toBe(IconPicker::class);
});

it('always resolves to tallstackui regardless of config — native mode is suspended', function () {
    // Native mode is temporarily disabled while its UI is being redesigned.
    // The resolver returns 'tallstackui' unconditionally until native is re-enabled.
    expect(resolveAdapter('tallstackui'))->toBe('tallstackui')
        ->and(resolveAdapter('auto'))->toBe('tallstackui')
        ->and(resolveAdapter('native'))->toBe('tallstackui');
});

it('always renders the tallstackui view', function () {
    $component = new IconPicker;
    $rendered = $component->render();

    expect($rendered->getName())->toBe('tall::livewire.icon-picker-tallstackui');
});
