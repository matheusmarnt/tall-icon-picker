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
    <div class="flex flex-col gap-1 sm:flex-row sm:items-center">

        <div class="group relative flex flex-1 items-center gap-3 rounded-lg border border-gray-300 bg-white
                    px-3 py-2.5 shadow-sm transition-all
                    focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500
                    dark:border-zinc-700 dark:bg-zinc-800/80 dark:focus-within:border-primary-500">

            @if ($value)
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md
                            bg-gray-50 text-gray-700 shadow-sm dark:bg-zinc-700 dark:text-gray-200">
                    {!! $this->selectedIconSvg !!}
                </div>
                <span class="flex-1 truncate text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ $value }}
                </span>

                <button
                    wire:click.stop="clearIcon"
                    type="button"
                    title="{{ __('tall-icon-picker::icon-picker.remove_icon') }}"
                    class="ml-auto rounded-md p-1 text-gray-400 transition-colors
                           hover:bg-red-50 hover:text-red-500
                           focus:outline-none focus:ring-2 focus:ring-red-500/50
                           dark:text-zinc-500 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @else
                <div class="flex h-6 w-6 shrink-0 items-center justify-center text-gray-400 dark:text-zinc-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                </div>
                <span class="flex-1 text-sm text-gray-400 dark:text-zinc-500">
                    {{ $placeholder ?: __('tall-icon-picker::icon-picker.no_icon_selected') }}
                </span>
            @endif
        </div>

        <x-tall::ui.button
            wire:click="$set('open', true)"
            type="button"
            color="primary"
            class="w-full justify-center sm:w-auto"
            icon="magnifying-glass"
        >
            {{ __('tall-icon-picker::icon-picker.choose') }}
        </x-tall::ui.button>
    </div>

    {{-- ── Drawer ───────────────────────────────────────────────── --}}
    <x-tall::ui.drawer
        property="open"
        :title="__('tall-icon-picker::icon-picker.choose_icon')"
        size="5xl"
    >
        <div class="flex flex-col gap-6">

            {{-- Filters & Search --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-tall::ui.select
                    wire:model.live="libraries"
                    :label="__('tall-icon-picker::icon-picker.icon_libraries')"
                    :hint="__('tall-icon-picker::icon-picker.libraries_hint')"
                    :options="$this->availableLibraries"
                    select="label:name|value:id"
                    :multiple="true"
                    :searchable="true"
                />

                <x-tall::ui.input
                    wire:model.live.debounce.300ms="search"
                    :label="__('tall-icon-picker::icon-picker.search_label')"
                    :hint="__('tall-icon-picker::icon-picker.search_hint')"
                    :placeholder="__('tall-icon-picker::icon-picker.search_placeholder')"
                    icon="magnifying-glass"
                />
            </div>

            {{-- Stats bar --}}
            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-2
                        text-xs font-medium text-gray-500
                        dark:bg-zinc-800/50 dark:text-zinc-400">
                <div class="flex items-center gap-2">
                    <span wire:loading.remove wire:target="search, libraries, page">
                        {{ __('tall-icon-picker::icon-picker.icons_count', ['count' => number_format($this->icons()->total())]) }}
                    </span>
                    <span wire:loading wire:target="search, libraries, page"
                          class="flex items-center gap-2 text-primary-500">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        {{ __('tall-icon-picker::icon-picker.loading') }}
                    </span>
                </div>

                @if ($this->icons()->total() > 0)
                    <span>
                        {{ __('tall-icon-picker::icon-picker.page_info', ['current' => $page, 'last' => $this->icons()->lastPage()]) }}
                    </span>
                @endif
            </div>

            {{-- Loading skeleton --}}
            <div
                wire:loading.flex
                wire:target="search, libraries, page"
                class="grid grid-cols-4 gap-3 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10"
            >
                @foreach (range(1, 40) as $_)
                    <div class="aspect-square animate-pulse rounded-xl bg-gray-200 dark:bg-zinc-800"></div>
                @endforeach
            </div>

            {{-- Icon grid --}}
            <div
                wire:loading.remove
                wire:target="search, libraries, page"
                class="grid grid-cols-4 gap-3 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10"
            >
                @forelse ($this->icons() as $icon)
                    <button
                        wire:key="icon-{{ $icon }}"
                        wire:click="selectIcon('{{ $icon }}')"
                        type="button"
                        title="{{ Str::after($icon, '-') }}"
                        @class([
                            'group relative flex aspect-square flex-col items-center justify-center gap-2 rounded-xl border p-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900',
                            'border-primary-500 bg-primary-50 ring-1 ring-primary-500 dark:bg-primary-900/20 dark:border-primary-500' => $value === $icon,
                            'border-gray-200 bg-white hover:-translate-y-1 hover:border-primary-300 hover:bg-gray-50 hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800/80 dark:hover:border-primary-600 dark:hover:bg-zinc-800' => $value !== $icon,
                        ])
                    >
                        {{-- Selected badge --}}
                        @if ($value === $icon)
                            <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center
                                         rounded-full bg-primary-500 text-white shadow-sm ring-2 ring-white dark:ring-zinc-900">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        @endif

                        {{-- Icon SVG --}}
                        <span class="flex items-center justify-center text-gray-700 transition-colors
                                     group-hover:text-primary-600 dark:text-gray-300 dark:group-hover:text-primary-400">
                            @php try { echo svg($icon, 'w-7 h-7')->toHtml(); } catch (\Throwable) {} @endphp
                        </span>

                        {{-- Icon name (sm+ only) --}}
                        <span class="hidden w-full truncate text-center text-[10px] font-medium
                                     text-gray-400 transition-colors group-hover:text-primary-500
                                     dark:text-zinc-500 sm:block">
                            {{ Str::after($icon, '-') }}
                        </span>
                    </button>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center rounded-xl
                                border border-dashed border-gray-300 bg-gray-50 py-16 text-center
                                dark:border-zinc-700 dark:bg-zinc-800/30">
                        <div class="mb-4 rounded-full bg-gray-200 p-3 dark:bg-zinc-700">
                            <svg class="h-6 w-6 text-gray-500 dark:text-zinc-400" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
                            </svg>
                        </div>
                        <h3 class="mb-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('tall-icon-picker::icon-picker.no_icons_found') }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-zinc-400">
                            {{ __('tall-icon-picker::icon-picker.no_icons_hint') }}
                        </p>

                        @if ($search || count($libraries) > 1)
                            <x-tall::ui.button
                                wire:click="resetFilters"
                                color="secondary"
                                variant="flat"
                                class="mt-4"
                                :sm="true"
                            >
                                {{ __('tall-icon-picker::icon-picker.clear_filters') }}
                            </x-tall::ui.button>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($this->icons()->lastPage() > 1)
                <div class="mt-4 flex flex-wrap items-center justify-center gap-1.5
                            border-t border-gray-200 pt-6 dark:border-zinc-700 sm:gap-2">

                    <button
                        wire:click="previousPage"
                        type="button"
                        @disabled($page <= 1)
                        aria-label="{{ __('tall-icon-picker::icon-picker.previous_page') }}"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200
                               bg-white text-gray-500 transition-all hover:bg-gray-50 hover:text-gray-700
                               disabled:pointer-events-none disabled:opacity-50
                               dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400
                               dark:hover:bg-zinc-700 dark:hover:text-gray-200"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    @php
                        $lastPage = $this->icons()->lastPage();
                        $start    = max(1, $page - 2);
                        $end      = min($lastPage, $page + 2);
                    @endphp

                    @if ($start > 1)
                        <button wire:click="goToPage(1)" type="button"
                                class="hidden h-9 w-9 items-center justify-center rounded-lg border border-gray-200
                                       bg-white text-sm font-medium text-gray-600 transition-all hover:bg-gray-50
                                       dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400
                                       dark:hover:bg-zinc-700 sm:flex">
                            1
                        </button>
                        @if ($start > 2)
                            <span class="hidden px-2 text-gray-400 sm:block">…</span>
                        @endif
                    @endif

                    @for ($p = $start; $p <= $end; $p++)
                        <button
                            wire:click="goToPage({{ $p }})"
                            type="button"
                            @class([
                                'flex h-9 min-w-[36px] items-center justify-center rounded-lg border text-sm font-medium transition-all px-2',
                                'border-primary-500 bg-primary-500 text-white shadow-sm dark:bg-primary-600 dark:border-primary-600' => $p === $page,
                                'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-gray-200' => $p !== $page,
                            ])
                        >{{ $p }}</button>
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="hidden px-2 text-gray-400 sm:block">…</span>
                        @endif
                        <button wire:click="goToPage({{ $lastPage }})" type="button"
                                class="hidden h-9 w-9 items-center justify-center rounded-lg border border-gray-200
                                       bg-white text-sm font-medium text-gray-600 transition-all hover:bg-gray-50
                                       dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400
                                       dark:hover:bg-zinc-700 sm:flex">
                            {{ $lastPage }}
                        </button>
                    @endif

                    <button
                        wire:click="nextPage"
                        type="button"
                        @disabled($page >= $this->icons()->lastPage())
                        aria-label="{{ __('tall-icon-picker::icon-picker.next_page') }}"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200
                               bg-white text-gray-500 transition-all hover:bg-gray-50 hover:text-gray-700
                               disabled:pointer-events-none disabled:opacity-50
                               dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400
                               dark:hover:bg-zinc-700 dark:hover:text-gray-200"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            @endif

        </div>

        <x-slot:footer>
            <div class="flex w-full justify-end">
                <x-tall::ui.button wire:click="$set('open', false)" color="secondary" variant="flat">
                    {{ __('tall-icon-picker::icon-picker.cancel') }}
                </x-tall::ui.button>
            </div>
        </x-slot:footer>

    </x-tall::ui.drawer>
</div>
