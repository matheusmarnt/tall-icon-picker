<?php

declare(strict_types=1);

use Matheusmarnt\TallIconPicker\TallIconPickerServiceProvider;
use ReflectionMethod;

function resolveAdapter(string $uiConfig): string
{
    config()->set('tall-icon-picker.ui', $uiConfig);
    $provider = new TallIconPickerServiceProvider(app());
    $method = new ReflectionMethod($provider, 'resolveUiAdapter');
    $method->setAccessible(true); // required — invoke() throws on private methods without this in PHP 8.2

    return $method->invoke($provider);
}

it('forces tallstackui when config is explicitly set to tallstackui', function () {
    expect(resolveAdapter('tallstackui'))->toBe('tallstackui');
});

it('forces native when config is explicitly set to native', function () {
    expect(resolveAdapter('native'))->toBe('native');
});

it('resolves to native on auto when TallStackUI is not installed', function () {
    // TallStackUI is in `suggest`, so it is NOT installed in the test environment.
    // class_exists(\TallStackUI\TallStackUIServiceProvider::class) therefore returns false.
    expect(resolveAdapter('auto'))->toBe('native');
})->skip(
    fn () => class_exists(\TallStackUI\TallStackUIServiceProvider::class),
    'TallStackUI is installed — auto resolves to tallstackui in this environment'
);
