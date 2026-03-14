<div wire:cloak
     x-data
     x-on:icon-picked.window="
        if ($event.detail.property && $wire.$parent) {
            $wire.$parent.$set($event.detail.property, $event.detail.value)
        }
    ">

    <div class="flex items-center gap-2">
        <div
            class="flex flex-1 items-center gap-2.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
            @if ($value)
                <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center text-gray-600 dark:text-gray-300">
                    {!! $this->selectedIconSvg !!}
                </span>
                <span class="flex-1 truncate text-gray-700 dark:text-gray-200">{{ $value }}</span>
                <button
                    wire:click="clearIcon"
                    type="button"
                    title="{{ __('tall-icon-picker::icon-picker.remove_icon') }}"
                    class="ml-auto text-gray-400 transition-colors hover:text-red-500 dark:text-zinc-500 dark:hover:text-red-400"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @else
                <span class="text-gray-400 dark:text-zinc-500">{{ __('tall-icon-picker::icon-picker.no_icon_selected') }}</span>
            @endif
        </div>
        <x-ts-button
            wire:click="$set('open', true)"
            type="button"
            color="secondary"
            sm
            icon="magnifying-glass"
        >
            {{ __('tall-icon-picker::icon-picker.choose') }}
        </x-ts-button>
    </div>

    <x-ts-slide wire="open" :title="__('tall-icon-picker::icon-picker.choose_icon')" size="6xl">

        <div class="flex flex-col gap-5">

            <x-ts-select.styled
                wire:model.live="libraries"
                :label="__('tall-icon-picker::icon-picker.icon_libraries')"
                :options="$this->availableLibraries"
                select="label:name|value:id"
                :multiple="true"
                :searchable="true"
            />

            <x-ts-input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('tall-icon-picker::icon-picker.search_placeholder')"
                icon="magnifying-glass"
            />

            <div class="flex items-center justify-between text-xs text-gray-400 dark:text-zinc-500">
                <span wire:loading.remove>
                    {{ __('tall-icon-picker::icon-picker.icons_count', ['count' => number_format($this->icons()->total())]) }}
                    @if ($search)
                        · {{ __('tall-icon-picker::icon-picker.filtered_by', ['term' => $search]) }}
                    @endif
                </span>
                <span wire:loading class="animate-pulse">{{ __('tall-icon-picker::icon-picker.loading') }}</span>
                <span>{{ __('tall-icon-picker::icon-picker.page_info', ['current' => $page, 'last' => $this->icons()->lastPage()]) }}</span>
            </div>

            <div
                wire:loading.flex
                class="grid grid-cols-5 gap-2 sm:grid-cols-6 md:grid-cols-8"
            >
                @foreach (range(1, 40) as $_)
                    <div class="h-16 animate-pulse rounded-xl bg-gray-100 dark:bg-zinc-800"></div>
                @endforeach
            </div>

            <div
                wire:loading.remove
                class="grid grid-cols-5 gap-2 transition-opacity duration-200 sm:grid-cols-6 md:grid-cols-8"
            >
                @forelse ($this->icons() as $icon)
                    <button
                        wire:key="icon-{{ $icon }}"
                        wire:click="selectIcon('{{ $icon }}')"
                        type="button"
                        title="{{ Str::after($icon, '-') }}"
                        @class([
                            'group relative flex flex-col items-center justify-center gap-1 rounded-xl border p-2 transition-all duration-150 cursor-pointer',
                            'border-blue-500 bg-blue-50 ring-2 ring-blue-500 dark:bg-blue-900/20' => $value === $icon,
                            'border-gray-100 dark:border-zinc-700 hover:scale-110 hover:border-blue-300 hover:bg-blue-50/50 dark:hover:border-blue-600 dark:hover:bg-blue-900/10' => $value !== $icon,
                        ])
                    >
                        @if ($value === $icon)
                            <span
                                class="absolute right-0.5 top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-blue-500">
                                <svg class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        @endif

                        <span class="flex h-6 w-6 items-center justify-center text-gray-600 dark:text-gray-300">
                            <x-dynamic-component :component="$icon" class="w-5 h-5"/>
                        </span>

                        <span
                            class="hidden w-full truncate text-center text-[9px] leading-tight text-gray-400 dark:text-zinc-500 sm:block">
                            {{ Str::after($icon, '-') }}
                        </span>
                    </button>
                @empty
                    <div
                        class="col-span-full flex flex-col items-center justify-center py-14 text-gray-400 dark:text-zinc-600">
                        <svg class="mb-3 h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
                        </svg>
                        <p class="text-sm font-medium">{{ __('tall-icon-picker::icon-picker.no_icons_found') }}</p>
                        <p class="mt-1 text-xs">{{ __('tall-icon-picker::icon-picker.no_icons_hint') }}</p>
                    </div>
                @endforelse
            </div>

            @if ($this->icons()->lastPage() > 1)
                <div class="flex items-center justify-center gap-1 border-t border-gray-100 pt-4 dark:border-zinc-700">
                    <button
                        wire:click="previousPage"
                        type="button"
                        @disabled($page <= 1)
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-sm text-gray-500 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
                    >‹
                    </button>

                    @php
                        $lastPage = $this->icons()->lastPage();
                        $start    = max(1, $page - 2);
                        $end      = min($lastPage, $page + 2);
                    @endphp

                    @if ($start > 1)
                        <button wire:click="goToPage(1)" type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-sm text-gray-500 transition-colors hover:bg-gray-50 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800">
                            1
                        </button>
                        @if ($start > 2)
                            <span class="px-1 text-gray-400">…</span>
                        @endif
                    @endif

                    @for ($p = $start; $p <= $end; $p++)
                        <button
                            wire:click="goToPage({{ $p }})"
                            type="button"
                            @class([
                                'flex h-8 w-8 items-center justify-center rounded-lg border text-sm transition-colors',
                                'border-blue-500 bg-blue-500 font-semibold text-white' => $p === $page,
                                'border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800' => $p !== $page,
                            ])
                        >{{ $p }}</button>
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="px-1 text-gray-400">…</span>
                        @endif
                        <button wire:click="goToPage({{ $lastPage }})" type="button"
                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-sm text-gray-500 transition-colors hover:bg-gray-50 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800">{{ $lastPage }}</button>
                    @endif

                    <button
                        wire:click="nextPage"
                        type="button"
                        @disabled($page >= $this->icons()->lastPage())
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-sm text-gray-500 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
                    >›
                    </button>
                </div>
            @endif

        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-ts-button wire:click="$set('open', false)" color="secondary" outline sm>
                    {{ __('tall-icon-picker::icon-picker.cancel') }}
                </x-ts-button>
            </div>
        </x-slot:footer>

    </x-ts-slide>
</div>