<div
    wire:cloak
    x-data
    x-on:icon-picked.window="
        if ($event.detail.property !== @js($parentModel)) return;
        const self = $el.closest('[wire\\:id]');
        const parent = self?.parentElement?.closest('[wire\\:id]');
        if (parent) {
            Livewire.find(parent.getAttribute('wire:id'))?.set(
                $event.detail.property,
                $event.detail.value
            );
        }
    "
>

    {{-- ── Trigger row ─────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Selected icon field --}}
        <button
            wire:click="$set('open', true)"
            type="button"
            class="flex flex-1 items-center gap-2.5 rounded-lg border border-gray-200 bg-white
                   px-3 py-2.5 text-sm transition-colors
                   hover:border-indigo-400/60 hover:bg-indigo-50/40
                   dark:border-zinc-700 dark:bg-zinc-800
                   dark:hover:border-indigo-500/40 dark:hover:bg-indigo-500/5
                   focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
        >
            @if ($value)
                <span class="flex h-5 w-5 shrink-0 items-center justify-center text-indigo-500 dark:text-indigo-400">
                    {!! $this->selectedIconSvg !!}
                </span>
                <span class="flex-1 truncate text-left text-gray-700 dark:text-gray-200">{{ $value }}</span>
            @else
                <span class="flex h-5 w-5 shrink-0 items-center justify-center text-gray-300 dark:text-zinc-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 16.5V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18v-1.5M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                    </svg>
                </span>
                <span class="flex-1 text-left text-gray-400 dark:text-zinc-500 ml-2">
                    {{ $placeholder ?: __('tall-icon-picker::icon-picker.no_icon_selected') }}
                </span>
            @endif

            <svg class="ml-auto h-4 w-4 shrink-0 text-gray-300 dark:text-zinc-600"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
            </svg>
        </button>

        {{-- Clear button — only when a value is set --}}
        @if ($value)
            <button
                wire:click="clearIcon"
                type="button"
                title="{{ __('tall-icon-picker::icon-picker.remove_icon') }}"
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200
                       text-gray-400 transition-colors
                       hover:border-red-200 hover:bg-red-50 hover:text-red-500
                       dark:border-zinc-700 dark:text-zinc-500
                       dark:hover:border-red-800/50 dark:hover:bg-red-900/20 dark:hover:text-red-400
                       focus:outline-none focus:ring-2 focus:ring-red-500/40"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif

        {{-- Open drawer button --}}
        <x-tall::ui.button
            wire:click="$set('open', true)"
            type="button"
            color="secondary"
            :sm="true"
            icon="magnifying-glass"
        >
            {{ __('tall-icon-picker::icon-picker.choose') }}
        </x-tall::ui.button>
    </div>

    {{-- ── Drawer ───────────────────────────────────────────────── --}}
    <x-tall::ui.drawer
        property="open"
        :title="__('tall-icon-picker::icon-picker.choose_icon')"
        size="6xl"
    >
        <div class="flex flex-col gap-4">

            {{-- Library selector --}}
            <x-tall::ui.select
                wire:model.live="libraries"
                :label="__('tall-icon-picker::icon-picker.icon_libraries')"
                :options="$this->availableLibraries"
                select="label:name|value:id"
                :multiple="true"
                :searchable="true"
            />

            {{-- Search --}}
            <x-tall::ui.input
                wire:model.live.debounce.300ms="search"
                :placeholder="__('tall-icon-picker::icon-picker.search_placeholder')"
                icon="magnifying-glass"
            />

            {{-- Stats bar --}}
            <div class="flex items-center justify-between text-xs text-gray-400 dark:text-zinc-500">
                <span wire:loading.remove>
                    {{ __('tall-icon-picker::icon-picker.icons_count', ['count' => number_format($this->icons()->total())]) }}
                    @if ($search)
                        <span class="opacity-70">·</span>
                        {{ __('tall-icon-picker::icon-picker.filtered_by', ['term' => $search]) }}
                    @endif
                </span>
                <span wire:loading class="animate-pulse">
                    {{ __('tall-icon-picker::icon-picker.loading') }}
                </span>
                <span>
                    {{ __('tall-icon-picker::icon-picker.page_info', ['current' => $page, 'last' => $this->icons()->lastPage()]) }}
                </span>
            </div>

            {{-- Loading skeleton --}}
            <div
                wire:loading.flex
                class="grid grid-cols-5 gap-2 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10"
            >
                @foreach (range(1, 50) as $_)
                    <div class="h-16 animate-pulse rounded-xl bg-gray-100 dark:bg-zinc-800"></div>
                @endforeach
            </div>

            {{-- Icon grid --}}
            <div
                wire:loading.remove
                class="grid grid-cols-5 gap-2 transition-opacity duration-200 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10"
            >
                @forelse ($this->icons() as $icon)
                    <button
                        wire:key="icon-{{ $icon }}"
                        wire:click="selectIcon('{{ $icon }}')"
                        type="button"
                        title="{{ Str::after($icon, '-') }}"
                        @class([
                            'group relative flex flex-col items-center justify-center gap-1 rounded-xl border p-2 transition-all duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/40',
                            'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500/50 dark:bg-indigo-900/20' => $value === $icon,
                            'border-gray-100 dark:border-zinc-700 hover:scale-105 hover:border-indigo-400/60 hover:bg-indigo-50/70 hover:shadow-sm dark:hover:border-indigo-500/40 dark:hover:bg-indigo-500/10' => $value !== $icon,
                        ])
                    >
                        {{-- Selected checkmark badge --}}
                        @if ($value === $icon)
                            <span class="absolute right-0.5 top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-violet-500">
                                <svg class="h-2.5 w-2.5 text-white" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        @endif

                        {{-- Icon SVG --}}
                        <span class="flex h-6 w-6 items-center justify-center text-gray-500 transition-colors duration-150 group-hover:text-indigo-600 dark:text-gray-400 dark:group-hover:text-indigo-400">
                            <x-dynamic-component :component="$icon" class="w-5 h-5"/>
                        </span>

                        {{-- Icon name (sm+ only) --}}
                        <span class="hidden w-full truncate text-center text-[9px] leading-tight text-gray-400 dark:text-zinc-500 sm:block">
                            {{ Str::after($icon, '-') }}
                        </span>
                    </button>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400 dark:text-zinc-600">
                        <svg class="mb-3 h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
                        </svg>
                        <p class="text-sm font-medium">{{ __('tall-icon-picker::icon-picker.no_icons_found') }}</p>
                        <p class="mt-1 text-xs opacity-70">{{ __('tall-icon-picker::icon-picker.no_icons_hint') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($this->icons()->lastPage() > 1)
                <div class="flex items-center justify-center gap-1 border-t border-gray-100 pt-4 dark:border-zinc-700">

                    <button
                        wire:click="previousPage"
                        type="button"
                        @disabled($page <= 1)
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-sm
                               text-gray-500 transition-colors hover:bg-gray-50
                               disabled:cursor-not-allowed disabled:opacity-40
                               dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
                    >‹</button>

                    @php
                        $lastPage = $this->icons()->lastPage();
                        $start    = max(1, $page - 2);
                        $end      = min($lastPage, $page + 2);
                    @endphp

                    {{-- Mobile: compact indicator --}}
                    <span class="px-2 text-sm text-gray-500 dark:text-zinc-400 sm:hidden">
                        {{ $page }}/{{ $lastPage }}
                    </span>

                    {{-- Desktop: numbered buttons --}}
                    @if ($start > 1)
                        <button wire:click="goToPage(1)" type="button"
                                class="hidden h-9 w-9 items-center justify-center rounded-lg border border-gray-200
                                       text-sm text-gray-500 transition-colors hover:bg-gray-50
                                       dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800 sm:flex">
                            1
                        </button>
                        @if ($start > 2)
                            <span class="hidden px-1 text-gray-300 dark:text-zinc-600 sm:inline">…</span>
                        @endif
                    @endif

                    @for ($p = $start; $p <= $end; $p++)
                        <button
                            wire:click="goToPage({{ $p }})"
                            type="button"
                            @class([
                                'hidden h-9 w-9 items-center justify-center rounded-lg border text-sm transition-colors sm:flex',
                                'border-indigo-500 bg-indigo-500 font-semibold text-white shadow-sm shadow-indigo-500/30' => $p === $page,
                                'border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800' => $p !== $page,
                            ])
                        >{{ $p }}</button>
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="hidden px-1 text-gray-300 dark:text-zinc-600 sm:inline">…</span>
                        @endif
                        <button wire:click="goToPage({{ $lastPage }})" type="button"
                                class="hidden h-9 w-9 items-center justify-center rounded-lg border border-gray-200
                                       text-sm text-gray-500 transition-colors hover:bg-gray-50
                                       dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800 sm:flex">
                            {{ $lastPage }}
                        </button>
                    @endif

                    <button
                        wire:click="nextPage"
                        type="button"
                        @disabled($page >= $this->icons()->lastPage())
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-sm
                               text-gray-500 transition-colors hover:bg-gray-50
                               disabled:cursor-not-allowed disabled:opacity-40
                               dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
                    >›</button>
                </div>
            @endif

        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-tall::ui.button
                    wire:click="$set('open', false)"
                    color="secondary"
                    :outline="true"
                    :sm="true"
                >
                    {{ __('tall-icon-picker::icon-picker.cancel') }}
                </x-tall::ui.button>
            </div>
        </x-slot:footer>

    </x-tall::ui.drawer>
</div>
