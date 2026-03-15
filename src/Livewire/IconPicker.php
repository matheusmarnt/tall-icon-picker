<?php

declare(strict_types=1);

namespace Matheusmarnt\TallIconPicker\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Matheusmarnt\TallIconPicker\Services\IconDiscoveryService;
use Throwable;

class IconPicker extends Component
{
    #[Modelable]
    public string $value = '';

    public string $parentModel = '';

    public string $placeholder = '';

    public bool $open = false;

    public string $search = '';

    /** @var array<string> */
    public array $libraries = ['lucide'];

    public int $page = 1;

    public int $perPage = 60;

    /** @return array<int, array{id: string, name: string}> */
    #[Computed(persist: true)]
    public function availableLibraries(): array
    {
        return app(IconDiscoveryService::class)->getAvailableLibraries();
    }

    /** @return LengthAwarePaginator<int, string> */
    #[Computed(persist: true)]
    public function icons(): LengthAwarePaginator
    {
        return app(IconDiscoveryService::class)->discoverIcons(
            $this->libraries,
            $this->search,
            $this->page,
            $this->perPage
        );
    }

    #[Computed]
    public function selectedIconSvg(): string
    {
        if (blank($this->value)) {
            return '';
        }

        try {
            return svg($this->value, 'w-5 h-5')->toHtml();
        } catch (Throwable) {
            return '';
        }
    }

    public function updatedSearch(): void
    {
        $this->page = 1;
        unset($this->icons);
    }

    public function updatedLibraries(): void
    {
        /** @var array<string, array{package: string, path: string, label: string}> $libraries */
        $libraries = Config::get('tall-icon-picker.libraries', []);
        $configuredLibraries = array_keys($libraries);

        $this->libraries = array_values(
            array_unique(array_intersect($this->libraries, $configuredLibraries))
        );

        $this->page = 1;
        $this->search = '';
        unset($this->icons);
    }

    public function selectIcon(string $icon): void
    {
        $this->value = $icon;
        $this->open = false;

        if ($this->parentModel !== '') {
            $this->dispatch('icon-picked', property: $this->parentModel, value: $icon);
        }
    }

    public function clearIcon(): void
    {
        $this->value = '';

        if ($this->parentModel !== '') {
            $this->dispatch('icon-picked', property: $this->parentModel, value: '');
        }
    }

    public function nextPage(): void
    {
        if ($this->page < $this->icons()->lastPage()) {
            $this->page++;
            unset($this->icons);
        }
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            unset($this->icons);
        }
    }

    public function goToPage(int $page): void
    {
        $this->page = max(1, min($page, $this->icons()->lastPage()));
        unset($this->icons);
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->libraries = ['lucide'];
        $this->page = 1;
        unset($this->icons);
    }

    public function render(): View
    {
        $ui = Config::get('tall-icon-picker.ui', 'native');

        /** @var view-string $view */
        $view = $ui === 'tallstackui'
            ? 'tall::livewire.icon-picker-tallstackui'
            : 'tall::livewire.icon-picker';

        return view($view);
    }
}
