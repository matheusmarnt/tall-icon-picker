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
    <div class="flex items-center gap-2">

        <div class="flex flex-1 items-center gap-2.5 rounded-lg border border-gray-200 bg-white
                    px-3 py-2 text-sm transition-colors
                    dark:border-zinc-700 dark:bg-zinc-800">

            @if ($value)
                <span class="flex h-5 w-5 shrink-0 items-center justify-center text-gray-700 dark:text-gray-300">
                    {!! $this->selectedIconSvg !!}
                </span>
                <span class="flex-1 truncate text-sm text-gray-700 dark:text-gray-200">
                    {{ $value }}
                </span>
                <button
                    wire:click="clearIcon"
                    type="button"
                    title="{{ __('tall-icon-picker::icon-picker.remove_icon') }}"
                    class="ml-auto shrink-0 rounded p-0.5 text-gray-400 transition-colors
                           hover:bg-red-50 hover:text-red-500
                           dark:text-zinc-500 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @else
                <span class="text-gray-400 dark:text-zinc-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                </span>
                <span class="flex-1 text-sm text-gray-400 dark:text-zinc-500">
                    {{ $placeholder ?: __('tall-icon-picker::icon-picker.no_icon_selected') }}
                </span>
            @endif
        </div>

        <x-tall::ui.button
            wire:click="$set('open', true)"
            type="button"
            color="primary"
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
        size="5xl"
    >
        <div class="flex flex-col gap-5">

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
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-zinc-400">
                <span wire:loading.remove wire:target="search, libraries, page">
                    {{ __('tall-icon-picker::icon-picker.icons_count', ['count' => number_format($this->icons()->total())]) }}
                    @if ($search)
                        <span class="mx-1 opacity-40">·</span>
                        <span class="font-medium text-blue-600 dark:text-blue-400">{{ $search }}</span>
                    @endif
                </span>
                <span wire:loading wire:target="search, libraries, page"
                      class="animate-pulse text-gray-400 dark:text-zinc-500">
                    {{ __('tall-icon-picker::icon-picker.loading') }}
                </span>

                @if ($this->icons()->total() > 0)
                    <span wire:loading.remove wire:target="search, libraries, page">
                        {{ __('tall-icon-picker::icon-picker.page_info', ['current' => $page, 'last' => $this->icons()->lastPage()]) }}
                    </span>
                @endif
            </div>

            {{-- Loading skeleton --}}
            <div
                wire:loading.flex
                wire:target="search, libraries, page"
                class="grid grid-cols-5 gap-2 sm:grid-cols-6 md:grid-cols-8"
            >
                @foreach (range(1, 48) as $_)
                    <div class="aspect-square animate-pulse rounded-lg bg-gray-100 dark:bg-zinc-800"></div>
                @endforeach
            </div>

            {{-- Icon grid --}}
            <div
                wire:loading.remove
                wire:target="search, libraries, page"
                class="grid grid-cols-5 gap-2 sm:grid-cols-6 md:grid-cols-8"
            >
                @forelse ($this->icons() as $icon)
                    <button
                        wire:key="icon-{{ $icon }}"
                        wire:click="selectIcon('{{ $icon }}')"
                        type="button"
                        title="{{ Str::after($icon, '-') }}"
                        @class([
                            'group relative flex aspect-square items-center justify-center rounded-lg border p-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1',
                            'border-blue-500 bg-blue-50 ring-2 ring-blue-500 dark:bg-blue-900/20 dark:border-blue-500' => $value === $icon,
                            'border-gray-200 bg-white hover:scale-110 hover:border-blue-300 hover:bg-blue-50/50 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-blue-700 dark:hover:bg-blue-900/10' => $value !== $icon,
                        ])
                    >
                        {{-- Selected badge --}}
                        @if ($value === $icon)
                            <span class="absolute right-0.5 top-0.5 flex h-4 w-4 items-center justify-center
                                         rounded-full bg-blue-500 text-white shadow-sm">
                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        @endif

                        {{-- Icon SVG --}}
                        <span class="flex items-center justify-center text-gray-600 transition-colors
                                     group-hover:text-blue-600 dark:text-gray-400 dark:group-hover:text-blue-400">
                            @php try { echo svg($icon, 'w-5 h-5')->toHtml(); } catch (\Throwable) {} @endphp
                        </span>
                    </button>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center rounded-xl
                                border border-dashed border-gray-200 bg-gray-50 py-14 text-center
                                dark:border-zinc-700 dark:bg-zinc-800/30">
                        <div class="mb-3 rounded-full bg-gray-100 p-3 dark:bg-zinc-700">
                            <svg class="h-5 w-5 text-gray-400 dark:text-zinc-400" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('tall-icon-picker::icon-picker.no_icons_found') }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-zinc-500">
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
                @php
                    $lastPage = $this->icons()->lastPage();
                    $start    = max(1, $page - 2);
                    $end      = min($lastPage, $page + 2);
                @endphp

                <div class="flex items-center justify-center gap-1 border-t border-gray-100 pt-4 dark:border-zinc-800">

                    {{-- Previous --}}
                    <button
                        wire:click="previousPage"
                        type="button"
                        @disabled($page <= 1)
                        aria-label="{{ __('tall-icon-picker::icon-picker.previous_page') }}"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white
                               text-sm text-gray-500 transition-all hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600
                               disabled:pointer-events-none disabled:opacity-40
                               dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400
                               dark:hover:border-blue-700 dark:hover:bg-blue-900/20 dark:hover:text-blue-400"
                    >‹</button>

                    {{-- Mobile: compact page indicator --}}
                    <span class="px-2 text-xs text-gray-500 dark:text-zinc-400 sm:hidden">
                        {{ $page }}/{{ $lastPage }}
                    </span>

                    {{-- Desktop: page numbers --}}
                    @if ($start > 1)
                        <button wire:click="goToPage(1)" type="button"
                                class="hidden h-8 w-8 items-center justify-center rounded-lg border border-gray-200
                                       bg-white text-sm font-medium text-gray-600 transition-all
                                       hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600
                                       dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 sm:flex">
                            1
                        </button>
                        @if ($start > 2)
                            <span class="hidden px-1 text-xs text-gray-300 dark:text-zinc-600 sm:block">…</span>
                        @endif
                    @endif

                    @for ($p = $start; $p <= $end; $p++)
                        <button
                            wire:click="goToPage({{ $p }})"
                            type="button"
                            @class([
                                'hidden h-8 w-8 items-center justify-center rounded-lg border text-sm font-medium transition-all sm:flex',
                                'border-blue-500 bg-blue-500 text-white shadow-sm' => $p === $page,
                                'border-gray-200 bg-white text-gray-600 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400' => $p !== $page,
                            ])
                        >{{ $p }}</button>
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="hidden px-1 text-xs text-gray-300 dark:text-zinc-600 sm:block">…</span>
                        @endif
                        <button wire:click="goToPage({{ $lastPage }})" type="button"
                                class="hidden h-8 w-8 items-center justify-center rounded-lg border border-gray-200
                                       bg-white text-sm font-medium text-gray-600 transition-all
                                       hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600
                                       dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 sm:flex">
                            {{ $lastPage }}
                        </button>
                    @endif

                    {{-- Next --}}
                    <button
                        wire:click="nextPage"
                        type="button"
                        @disabled($page >= $this->icons()->lastPage())
                        aria-label="{{ __('tall-icon-picker::icon-picker.next_page') }}"
                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white
                               text-sm text-gray-500 transition-all hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600
                               disabled:pointer-events-none disabled:opacity-40
                               dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400
                               dark:hover:border-blue-700 dark:hover:bg-blue-900/20 dark:hover:text-blue-400"
                    >›</button>
                </div>
            @endif

        </div>

        <x-slot:footer>
            <div class="flex w-full justify-end">
                <x-tall::ui.button wire:click="$set('open', false)" color="secondary" variant="flat" :sm="true">
                    {{ __('tall-icon-picker::icon-picker.cancel') }}
                </x-tall::ui.button>
            </div>
        </x-slot:footer>

    </x-tall::ui.drawer>
</div>
