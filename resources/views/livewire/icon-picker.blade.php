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
                    wire:click="clearIcon"
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

        <button
            wire:click="$set('open', true)"
            type="button"
            class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-transparent
                   bg-primary-500 px-4 py-2 text-sm font-medium text-white shadow-sm transition-all duration-200
                   hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1
                   active:scale-95 disabled:pointer-events-none disabled:opacity-50
                   dark:focus:ring-offset-zinc-900 sm:w-auto"
        >
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
            </svg>
            {{ __('tall-icon-picker::icon-picker.choose') }}
        </button>
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

                {{-- Library multi-select (inline Alpine — v1.3.0 binding pattern) --}}
                @php
                    $libraryOptions = $this->availableLibraries;
                    $libraryJson = collect($libraryOptions)
                        ->map(fn ($opt) => ['label' => $opt['name'], 'value' => $opt['id']])
                        ->values()
                        ->toJson(JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                @endphp

                <div class="flex flex-col gap-1"
                     data-options="{{ $libraryJson }}"
                     data-selected-text="{{ __('tall-icon-picker::icon-picker.selected') }}"
                     data-placeholder-text="{{ __('tall-icon-picker::icon-picker.select_placeholder') }}"
                     x-data="{
                         open: false,
                         search: '',
                         options: JSON.parse($el.dataset.options),
                         selectedText: $el.dataset.selectedText,
                         placeholderText: $el.dataset.placeholderText,
                         get selected() { return $wire.libraries || []; },
                         get filtered() {
                             if (!this.search) return this.options;
                             const q = this.search.toLowerCase();
                             return this.options.filter(o => o.label.toLowerCase().includes(q));
                         },
                         toggle(val) {
                             const updated = this.selected.includes(val)
                                 ? this.selected.filter(v => v !== val)
                                 : [...this.selected, val];
                             $wire.set('libraries', updated);
                         },
                         isSelected(val) { return this.selected.includes(val); },
                         get triggerText() {
                             if (this.selected.length > 0) {
                                 return this.selected.length + ' ' + this.selectedText;
                             }
                             return this.placeholderText;
                         }
                     }"
                     @click.away="open = false"
                     @keydown.escape="open = false"
                >
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('tall-icon-picker::icon-picker.icon_libraries') }}
                    </label>

                    <div class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            class="flex w-full items-center justify-between rounded-lg border border-gray-300
                                   bg-white px-3 py-2.5 text-sm shadow-sm transition-all
                                   focus:outline-none focus:ring-2 focus:ring-primary-500
                                   dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-gray-200"
                        >
                            <span class="truncate text-gray-700 dark:text-gray-200" x-text="triggerText"></span>
                            <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition.opacity
                            x-cloak
                            class="absolute z-50 mt-1 w-full overflow-hidden rounded-lg border border-gray-200
                                   bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            <div class="border-b border-gray-100 p-2 dark:border-zinc-700">
                                <input
                                    x-model="search"
                                    type="text"
                                    placeholder="{{ __('tall-icon-picker::icon-picker.search_placeholder') }}"
                                    class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-1.5 text-sm
                                           focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500
                                           dark:border-zinc-600 dark:bg-zinc-900 dark:text-gray-200"
                                />
                            </div>

                            <div class="max-h-60 overflow-y-auto p-1">
                                <template x-for="option in filtered" :key="option.value">
                                    <button
                                        type="button"
                                        @click="toggle(option.value)"
                                        :class="isSelected(option.value)
                                            ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400'
                                            : 'text-gray-700 hover:bg-gray-50 dark:text-zinc-300 dark:hover:bg-zinc-700'"
                                        class="flex w-full items-center gap-2 rounded-md px-3 py-2
                                               text-left text-sm transition-colors"
                                    >
                                        <span class="flex h-4 w-4 shrink-0 items-center justify-center">
                                            <template x-if="isSelected(option.value)">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                                     stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </template>
                                        </span>
                                        <span x-text="option.label"></span>
                                    </button>
                                </template>

                                <template x-if="filtered.length === 0">
                                    <p class="px-3 py-2 text-sm text-gray-400 dark:text-zinc-500">
                                        {{ __('tall-icon-picker::icon-picker.no_icons_found') }}
                                    </p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <span class="text-xs text-gray-500 dark:text-zinc-400">
                        {{ __('tall-icon-picker::icon-picker.libraries_hint') }}
                    </span>
                </div>

                {{-- Search input --}}
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('tall-icon-picker::icon-picker.search_label') }}
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400 dark:text-zinc-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('tall-icon-picker::icon-picker.search_placeholder') }}"
                            class="w-full rounded-lg border py-2.5 pl-10 pr-4 text-sm shadow-sm transition-all duration-200
                                   focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500
                                   border-gray-300 bg-white text-gray-900 placeholder-gray-400
                                   dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-gray-200 dark:placeholder-zinc-500"
                        />
                    </div>
                    <span class="text-xs text-gray-500 dark:text-zinc-400">
                        {{ __('tall-icon-picker::icon-picker.search_hint') }}
                    </span>
                </div>
            </div>

            {{-- Stats bar --}}
            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-2
                        text-xs font-medium text-gray-500
                        dark:bg-zinc-800/50 dark:text-zinc-400">
                <div class="flex items-center gap-2">
                    <span wire:loading.remove wire:target="search, libraries, page">
                        {{ __('tall-icon-picker::icon-picker.icons_count', ['count' => number_format($this->icons()->total())]) }}
                        @if ($search)
                            <span class="mx-1 opacity-50">·</span>
                            <span class="font-medium text-primary-600 dark:text-primary-400">{{ $search }}</span>
                        @endif
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
                            <button
                                wire:click="resetFilters"
                                type="button"
                                class="mt-4 inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent
                                       px-3 py-1.5 text-xs font-medium text-gray-600 transition-all duration-200
                                       hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2
                                       focus:ring-gray-200 focus:ring-offset-1 active:scale-95
                                       dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                            >
                                {{ __('tall-icon-picker::icon-picker.clear_filters') }}
                            </button>
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

                    {{-- Mobile: compact indicator --}}
                    <span class="px-2 text-sm text-gray-500 dark:text-zinc-400 sm:hidden">
                        {{ $page }}/{{ $lastPage }}
                    </span>

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
                                'hidden h-9 min-w-[36px] items-center justify-center rounded-lg border text-sm font-medium transition-all px-2 sm:flex',
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
                <button
                    wire:click="$set('open', false)"
                    type="button"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-transparent
                           px-4 py-2 text-sm font-medium text-gray-600 transition-all duration-200
                           hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2
                           focus:ring-gray-200 focus:ring-offset-1 active:scale-95
                           dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                >
                    {{ __('tall-icon-picker::icon-picker.cancel') }}
                </button>
            </div>
        </x-slot:footer>

    </x-tall::ui.drawer>
</div>
