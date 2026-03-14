<?php

declare(strict_types=1);

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;
use Matheusmarnt\TallIconPicker\Livewire\IconPicker;
use Matheusmarnt\TallIconPicker\Services\IconDiscoveryService;

it('renders successfully', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    $mockService->shouldReceive('discoverIcons')->andReturn(new LengthAwarePaginator([], 0, 60, 1));
    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->assertStatus(200);
});

it('can search icons', function () {
    // Mock the service to avoid file system dependency in Feature test
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')
        ->andReturn([
            ['id' => 'lucide', 'name' => 'Lucide (100)'],
        ]);

    $mockService->shouldReceive('discoverIcons')
        ->with(['lucide'], 'home', 1, 60)
        ->andReturn(new LengthAwarePaginator(
            ['lucide-home'],
            1,
            60,
            1
        ));

    $mockService->shouldReceive('discoverIcons') // Initial load
        ->zeroOrMoreTimes()
        ->andReturn(new LengthAwarePaginator([], 0, 60, 1));

    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->set('search', 'home')
        ->assertSee('lucide-home');
});

it('dispatches event when icon is selected', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    $mockService->shouldReceive('discoverIcons')->andReturn(new LengthAwarePaginator(['lucide-home'], 1, 60, 1));
    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->set('parentModel', 'hero_icon')
        ->call('selectIcon', 'lucide-home')
        ->assertDispatched('icon-picked', property: 'hero_icon', value: 'lucide-home')
        ->assertSet('value', 'lucide-home')
        ->assertSet('open', false);
});

it('clears the selected icon', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    $mockService->shouldReceive('discoverIcons')->andReturn(new LengthAwarePaginator([], 0, 60, 1));
    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->set('value', 'lucide-home')
        ->call('clearIcon')
        ->assertSet('value', '');
});

it('advances to next page', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    // 121 total, 60 per page → lastPage = 3
    $mockService->shouldReceive('discoverIcons')->andReturn(
        new LengthAwarePaginator([], 121, 60, 1)
    );
    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->assertSet('page', 1)
        ->call('nextPage')
        ->assertSet('page', 2);
});

it('does not advance past last page', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    // 60 total, 60 per page → lastPage = 1
    $mockService->shouldReceive('discoverIcons')->andReturn(
        new LengthAwarePaginator([], 60, 60, 1)
    );
    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->call('nextPage')
        ->assertSet('page', 1); // if (1 < 1) is false — stays at 1
});

it('goes back to previous page', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    $mockService->shouldReceive('discoverIcons')->andReturn(
        new LengthAwarePaginator([], 121, 60, 1)
    );
    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->set('page', 2)
        ->call('previousPage')
        ->assertSet('page', 1);
});

it('does not go below page 1', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    $mockService->shouldReceive('discoverIcons')->andReturn(
        new LengthAwarePaginator([], 0, 60, 1)
    );
    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->call('previousPage')
        ->assertSet('page', 1); // if (1 > 1) is false — stays at 1
});

it('navigates to a specific page', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    // 180 total, 60 per page → lastPage = 3
    $mockService->shouldReceive('discoverIcons')->andReturn(
        new LengthAwarePaginator([], 180, 60, 1)
    );
    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->call('goToPage', 2)
        ->assertSet('page', 2);
});

it('clamps goToPage to valid range', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    // 60 total, 60 per page → lastPage = 1
    $mockService->shouldReceive('discoverIcons')->andReturn(
        new LengthAwarePaginator([], 60, 60, 1)
    );
    $this->app->instance(IconDiscoveryService::class, $mockService);

    Livewire::test(IconPicker::class)
        ->call('goToPage', 99)   // max(1, min(99, 1)) = 1
        ->assertSet('page', 1)
        ->call('goToPage', 0)    // max(1, min(0, 1)) = 1
        ->assertSet('page', 1);
});

it('filters out invalid libraries and resets pagination', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    $mockService->shouldReceive('discoverIcons')->andReturn(new LengthAwarePaginator([], 0, 60, 1));
    $this->app->instance(IconDiscoveryService::class, $mockService);

    // Set search first (triggers updatedSearch which resets page to 1),
    // then set page to 3 (no hook fires for page), then change libraries.
    Livewire::test(IconPicker::class)
        ->set('search', 'arrow')
        ->set('page', 3)
        ->set('libraries', ['lucide', 'nonexistent'])
        ->assertSet('libraries', ['lucide']) // 'nonexistent' filtered out
        ->assertSet('page', 1)              // page reset by updatedLibraries
        ->assertSet('search', '');          // search reset by updatedLibraries
});

it('renders in english locale', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    $mockService->shouldReceive('discoverIcons')->andReturn(new LengthAwarePaginator([], 0, 60, 1));
    $this->app->instance(IconDiscoveryService::class, $mockService);

    app()->setLocale('en');

    Livewire::test(IconPicker::class)
        ->assertSee('No icon selected');
});

it('renders in pt_BR locale', function () {
    $mockService = Mockery::mock(IconDiscoveryService::class);
    $mockService->shouldReceive('getAvailableLibraries')->andReturn([]);
    $mockService->shouldReceive('discoverIcons')->andReturn(new LengthAwarePaginator([], 0, 60, 1));
    $this->app->instance(IconDiscoveryService::class, $mockService);

    app()->setLocale('pt_BR');

    Livewire::test(IconPicker::class)
        ->assertSee('Nenhum ícone selecionado');
});
