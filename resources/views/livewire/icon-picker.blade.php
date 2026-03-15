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
            class="flex flex-1 items-center gap-2.5 rounded-xl border border-gray-200 bg-white
                   px-3.5 py-2.5 text-sm transition-all duration-200
                   hover:border-indigo-400/60 hover:bg-indigo-50/40 hover:shadow-sm
                   dark:border-zinc-700 dark:bg-zinc-800/80
                   dark:hover:border-indigo-500/40 dark:hover:bg-indigo-500/5
                   focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400"
        >
            @if ($value)
                <span class="flex h-5 w-5 shrink-0 items-center justify-center text-indigo-500 dark:text-indigo-400">
                    {!! $this->selectedIconSvg !!}
                </span>
                <span class="flex-1 truncate text-left font-medium text-gray-700 dark:text-gray-200">{{ $value }}</span>
            @else
                <span class="flex h-5 w-5 shrink-0 items-center justify-center text-gray-300 dark:text-zinc-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 16.5V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18v-1.5M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"/>
                    </svg>
                </span>
                <span class="flex-1 text-left text-gray-400 dark:text-zinc-500">
                    {{ $placeholder ?: __('tall-icon-picker::icon-picker.no_icon_selected') }}
                </span>
            @endif

            <svg class="ml-auto h-4 w-4 shrink-0 text-gray-300 dark:text-zinc-600"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
            </svg>
        </button>

        {{-- Clear button — only when a value is set --}}
        @if ($value)
            <button
                wire:click="clearIcon"
                type="button"
                title="{{ __('tall-icon-picker::icon-picker.remove_icon') }}"
                class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200
                       text-gray-400 transition-all duration-150
                       hover:border-red-200 hover:bg-red-50 hover:text-red-500 hover:shadow-sm
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
        <button
            wire:click="$set('open', true)"
            type="button"
            class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50
                   px-3.5 py-2 text-sm font-medium text-indigo-700 transition-all duration-150
                   hover:border-indigo-300 hover:bg-indigo-100 hover:shadow-sm
                   dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-400
                   dark:hover:border-indigo-500/50 dark:hover:bg-indigo-500/20
                   focus:outline-none focus:ring-2 focus:ring-indigo-500/40 active:scale-95"
        >
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
            </svg>
            {{ __('tall-icon-picker::icon-picker.choose') }}
        </button>
    </div>

    {{-- ── Drawer ───────────────────────────────────────────────── --}}
    <x-tall::ui.drawer
        property="open"
        :title="__('tall-icon-picker::icon-picker.choose_icon')"
        size="6xl"
    >
        <div class="flex flex-col gap-4">

            {{-- Filters panel --}}
            <div class="grid grid-cols-1 gap-3 rounded-xl bg-gray-50 p-3
                        dark:bg-zinc-800/60 md:grid-cols-2">

                {{-- Library selector --}}
                @php
                    $libraryOptions = $this->availableLibraries;
                    $libraryJson = collect($libraryOptions)
                        ->map(fn ($opt) => ['label' => $opt['name'], 'value' => $opt['id']])
                        ->values()
                        ->toJson(JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                @endphp

                <div
                    data-options="{{ $libraryJson }}"
                    x-data="{
                        open: false,
                        search: '',
                        options: JSON.parse($el.dataset.options),
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
                        remove(val) {
                            $wire.set('libraries', this.selected.filter(v => v !== val));
                        },
                        isSelected(val) { return this.selected.includes(val); },
                        labelFor(val) {
                            const opt = this.options.find(o => o.value === val);
                            return opt ? opt.label : val;
                        }
                    }"
                    @click.away="open = false"
                    @keydown.escape="open = false"
                    class="relative"
                >
                    <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-zinc-400">
                        {{ __('tall-icon-picker::icon-picker.icon_libraries') }}
                    </label>

                    <button
                        type="button"
                        @click="open = !open"
                        class="flex min-h-[38px] w-full flex-wrap items-center gap-1.5 rounded-lg
                               border border-gray-200 bg-white px-3 py-1.5 text-left text-sm
                               transition-all duration-200
                               hover:border-indigo-400/60 hover:bg-indigo-50/30
                               dark:border-zinc-700 dark:bg-zinc-800/50
                               focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400"
                    >
                        <template x-if="selected.length === 0">
                            <span class="text-gray-400 dark:text-zinc-500 text-sm">
                                {{ __('tall-icon-picker::icon-picker.no_icon_selected') }}
                            </span>
                        </template>

                        <template x-for="val in selected" :key="val">
                            <span class="inline-flex items-center gap-1 rounded-full
                                         bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700
                                         dark:bg-indigo-500/15 dark:text-indigo-300">
                                <span x-text="labelFor(val)"></span>
                                <button
                                    type="button"
                                    @click.stop="remove(val)"
                                    class="ml-0.5 text-indigo-500 transition-colors hover:text-indigo-700
                                           dark:text-indigo-400 dark:hover:text-indigo-200"
                                >
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                         stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <span class="ml-auto shrink-0 text-gray-400 transition-transform duration-200 dark:text-zinc-500"
                              :class="{ 'rotate-180': open }">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </button>

                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        x-cloak
                        class="absolute z-50 mt-1.5 w-full rounded-xl
                               border border-gray-200 bg-white shadow-lg shadow-gray-200/60
                               dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/30"
                    >
                        <div class="border-b border-gray-100 p-2 dark:border-zinc-800">
                            <input
                                type="text"
                                x-model="search"
                                placeholder="{{ __('tall-icon-picker::icon-picker.search_placeholder') }}"
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5
                                       text-sm text-gray-900 placeholder-gray-400
                                       dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:placeholder-zinc-500
                                       focus:outline-none focus:ring-1 focus:ring-indigo-500/50 focus:border-indigo-400"
                            />
                        </div>

                        <div class="max-h-52 overflow-y-auto p-1">
                            <template x-for="option in filtered" :key="option.value">
                                <button
                                    type="button"
                                    @click="toggle(option.value)"
                                    :class="isSelected(option.value)
                                        ? 'text-indigo-700 bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-500/10'
                                        : 'text-gray-700 dark:text-zinc-300'"
                                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2
                                           text-left text-sm transition-colors
                                           hover:bg-indigo-50/80 dark:hover:bg-indigo-500/15"
                                >
                                    <span class="flex h-4 w-4 shrink-0 items-center justify-center">
                                        <template x-if="isSelected(option.value)">
                                            <svg class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400"
                                                 fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M5 13l4 4L19 7"/>
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

                {{-- Search input --}}
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-zinc-400">
                        {{ __('tall-icon-picker::icon-picker.search_placeholder') }}
                    </label>
                    <div class="relative flex items-center">
                        <span class="pointer-events-none absolute left-3 text-gray-400 dark:text-zinc-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('tall-icon-picker::icon-picker.search_placeholder') }}"
                            class="w-full rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-4 text-sm
                                   text-gray-900 placeholder-gray-400 transition-all duration-200
                                   dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-100 dark:placeholder-zinc-500
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400
                                   dark:focus:border-indigo-500"
                        />
                    </div>
                </div>
            </div>

            {{-- Stats bar --}}
            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2
                        text-xs text-gray-500 dark:bg-zinc-800/40 dark:text-zinc-400">
                <span wire:loading.remove wire:target="search, libraries, page">
                    {{ __('tall-icon-picker::icon-picker.icons_count', ['count' => number_format($this->icons()->total())]) }}
                    @if ($search)
                        <span class="mx-1 opacity-50">·</span>
                        <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $search }}</span>
                    @endif
                </span>
                <span wire:loading wire:target="search, libraries, page" class="animate-pulse">
                    {{ __('tall-icon-picker::icon-picker.loading') }}
                </span>
                <span>
                    {{ __('tall-icon-picker::icon-picker.page_info', ['current' => $page, 'last' => $this->icons()->lastPage()]) }}
                </span>
            </div>

            {{-- Loading skeleton --}}
            <div
                wire:loading.flex wire:target="search, libraries, page"
                class="grid grid-cols-5 gap-2 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10"
            >
                @foreach (range(1, 60) as $_)
                    <div class="aspect-square animate-pulse rounded-xl bg-gray-100 dark:bg-zinc-800"></div>
                @endforeach
            </div>

            {{-- Icon grid --}}
            <div
                wire:loading.remove wire:target="search, libraries, page"
                class="grid grid-cols-5 gap-2 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10"
            >
                @forelse ($this->icons() as $icon)
                    <button
                        wire:key="icon-{{ $icon }}"
                        wire:click="selectIcon('{{ $icon }}')"
                        type="button"
                        title="{{ Str::after($icon, '-') }}"
                        @class([
                            'group relative flex aspect-square flex-col items-center justify-center gap-1 rounded-xl border p-2 transition-all duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/40',
                            'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-500/50 dark:bg-indigo-900/20' => $value === $icon,
                            'border-gray-100 dark:border-zinc-700 hover:-translate-y-1 hover:border-indigo-400/60 hover:bg-indigo-50/70 hover:shadow-md dark:hover:border-indigo-500/40 dark:hover:bg-indigo-500/10 dark:hover:shadow-zinc-900/50' => $value !== $icon,
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
                    <div class="col-span-full flex flex-col items-center justify-center rounded-xl
                                border border-dashed border-gray-200 py-16
                                text-gray-400 dark:border-zinc-700 dark:text-zinc-600">
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
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200
                               text-gray-500 transition-all
                               hover:border-gray-300 hover:bg-gray-50
                               disabled:cursor-not-allowed disabled:opacity-40
                               dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
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

                    {{-- Desktop: numbered buttons --}}
                    @if ($start > 1)
                        <button wire:click="goToPage(1)" type="button"
                                class="hidden h-9 w-9 items-center justify-center rounded-xl border border-gray-200
                                       text-sm text-gray-500 transition-all hover:bg-gray-50
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
                                'hidden h-9 w-9 items-center justify-center rounded-xl border text-sm transition-all sm:flex',
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
                                class="hidden h-9 w-9 items-center justify-center rounded-xl border border-gray-200
                                       text-sm text-gray-500 transition-all hover:bg-gray-50
                                       dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800 sm:flex">
                            {{ $lastPage }}
                        </button>
                    @endif

                    <button
                        wire:click="nextPage"
                        type="button"
                        @disabled($page >= $this->icons()->lastPage())
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200
                               text-gray-500 transition-all
                               hover:border-gray-300 hover:bg-gray-50
                               disabled:cursor-not-allowed disabled:opacity-40
                               dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            @endif

        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <button
                    wire:click="$set('open', false)"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white
                           px-3.5 py-2 text-sm font-medium text-gray-600 transition-all duration-150
                           hover:border-gray-300 hover:bg-gray-50
                           dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300
                           dark:hover:border-zinc-600 dark:hover:bg-zinc-700
                           focus:outline-none focus:ring-2 focus:ring-gray-400/40 active:scale-95"
                >
                    {{ __('tall-icon-picker::icon-picker.cancel') }}
                </button>
            </div>
        </x-slot:footer>

    </x-tall::ui.drawer>
</div>
