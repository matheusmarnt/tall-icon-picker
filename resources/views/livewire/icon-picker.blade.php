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

        {{-- Field — clicking anywhere on it opens the picker --}}
        <button
                wire:click="$set('open', true)"
                type="button"
                class="group flex flex-1 items-center gap-2.5 rounded-xl border border-zinc-200 bg-white
                       px-3.5 py-2.5 text-sm transition-all duration-200
                       hover:border-zinc-300 hover:bg-zinc-50
                       focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-500/20
                       dark:border-zinc-700 dark:bg-zinc-900
                       dark:hover:border-zinc-600 dark:hover:bg-zinc-800/80"
        >
            @if ($value)
                <span class="flex h-5 w-5 shrink-0 items-center justify-center text-violet-600 dark:text-violet-400">
                    {!! $this->selectedIconSvg !!}
                </span>
                <span class="flex-1 truncate text-left font-mono text-xs tracking-tight text-zinc-600 dark:text-zinc-300">
                    {{ $value }}
                </span>
                <span class="ml-auto flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-900/40">
                    <svg class="h-2.5 w-2.5 text-violet-600 dark:text-violet-400" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </span>
            @else
                <span class="flex h-5 w-5 shrink-0 items-center justify-center text-zinc-300 dark:text-zinc-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zm0 9.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zm9.75-9.75A2.25 2.25 0 0115.75 3.75H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zm0 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                </span>
                <span class="flex-1 text-left text-zinc-400 dark:text-zinc-500">
                    {{ $placeholder ?: __('tall-icon-picker::icon-picker.no_icon_selected') }}
                </span>
                <svg class="ml-auto h-3.5 w-3.5 shrink-0 text-zinc-300 dark:text-zinc-600 group-hover:text-zinc-400"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
                </svg>
            @endif
        </button>

        {{-- Clear button — only shown when a value is set --}}
        @if ($value)
            <button
                    wire:click="clearIcon"
                    type="button"
                    title="{{ __('tall-icon-picker::icon-picker.remove_icon') }}"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-zinc-200
                           text-zinc-400 transition-all duration-150
                           hover:border-red-200 hover:bg-red-50 hover:text-red-500
                           focus:outline-none focus:ring-2 focus:ring-red-500/30
                           dark:border-zinc-700 dark:text-zinc-500
                           dark:hover:border-red-800/50 dark:hover:bg-red-900/20 dark:hover:text-red-400"
            >
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        @endif

        {{-- Browse button --}}
        <button
                wire:click="$set('open', true)"
                type="button"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border border-violet-200 bg-violet-50
                       px-3.5 py-2.5 text-sm font-medium text-violet-700 transition-all duration-150
                       hover:border-violet-300 hover:bg-violet-100
                       focus:outline-none focus:ring-2 focus:ring-violet-500/30 active:scale-[0.97]
                       dark:border-violet-700/40 dark:bg-violet-900/20 dark:text-violet-300
                       dark:hover:border-violet-600/50 dark:hover:bg-violet-900/30"
        >
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
            </svg>
            <span class="hidden sm:inline">{{ __('tall-icon-picker::icon-picker.choose') }}</span>
        </button>
    </div>

    {{-- ── Drawer ───────────────────────────────────────────────── --}}
    <x-tall::ui.drawer
            property="open"
            :title="__('tall-icon-picker::icon-picker.choose_icon')"
            size="6xl"
    >
        <div class="flex flex-col gap-4">

            {{-- ── Filter panel ────────────────────────────────── --}}
            <div class="space-y-3 rounded-xl bg-zinc-50/80 p-3.5 ring-1 ring-zinc-100 dark:bg-zinc-800/30 dark:ring-zinc-700/50">

                {{-- Search input --}}
                <div class="relative flex items-center">
                    <span class="pointer-events-none absolute left-3 text-zinc-400 dark:text-zinc-500">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
                        </svg>
                    </span>
                    <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('tall-icon-picker::icon-picker.search_placeholder') }}"
                            class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-8 pr-4 text-sm
                                   text-zinc-900 placeholder-zinc-400 transition-all
                                   dark:border-zinc-700 dark:bg-zinc-800/50 dark:text-zinc-100 dark:placeholder-zinc-500
                                   focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:focus:border-violet-500"
                    />
                </div>

                {{-- Library toggle chips ──────────────────────── --}}
                {{-- Alpine reads available libs from data-libs; selection syncs via $wire.libraries --}}
                @php
                    $libJson = collect($this->availableLibraries)
                        ->map(fn ($l) => ['id' => $l['id'], 'name' => $l['name']])
                        ->values()
                        ->toJson(JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                @endphp

                <div
                        data-libs="{{ $libJson }}"
                        x-data="{
                            libs: JSON.parse($el.dataset.libs),
                            get selected() { return $wire.libraries || []; },
                            isOn(id) { return this.selected.includes(id); },
                            toggle(id) {
                                const cur = this.selected;
                                const next = cur.includes(id) ? cur.filter(v => v !== id) : [...cur, id];
                                if (next.length > 0) $wire.set('libraries', next);
                            }
                        }"
                >
                    <p class="mb-1.5 text-xs font-medium text-zinc-400 dark:text-zinc-500">
                        {{ __('tall-icon-picker::icon-picker.icon_libraries') }}
                    </p>

                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="lib in libs" :key="lib.id">
                            <button
                                    type="button"
                                    @click="toggle(lib.id)"
                                    :class="isOn(lib.id)
                                        ? 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-700/40 dark:bg-violet-900/20 dark:text-violet-300'
                                        : 'border-zinc-200 bg-white text-zinc-500 hover:border-zinc-300 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-400 dark:hover:border-zinc-600 dark:hover:text-zinc-300'"
                                    class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-violet-500/30"
                            >
                                <span class="h-1.5 w-1.5 rounded-full transition-colors"
                                      :class="isOn(lib.id) ? 'bg-violet-500' : 'bg-zinc-300 dark:bg-zinc-600'"></span>
                                <span x-text="lib.name"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ── Stats bar ───────────────────────────────────── --}}
            <div class="flex items-center justify-between font-mono text-xs tabular-nums text-zinc-400 dark:text-zinc-500">
                <span wire:loading.remove wire:target="search, libraries, page">
                    <span class="text-zinc-500 dark:text-zinc-400">{{ number_format($this->icons()->total()) }}</span>
                    <span class="mx-1 opacity-40">icons</span>
                    @if ($search)
                        <span class="mx-1 opacity-30">·</span>
                        <span class="text-violet-500 dark:text-violet-400">{{ $search }}</span>
                    @endif
                </span>
                <span wire:loading wire:target="search, libraries, page" class="animate-pulse">
                    {{ __('tall-icon-picker::icon-picker.loading') }}
                </span>

                @if ($this->icons()->total() > 0)
                    <span wire:loading.remove wire:target="search, libraries, page">
                        {{ $page }}&thinsp;/&thinsp;{{ $this->icons()->lastPage() }}
                    </span>
                @endif
            </div>

            {{-- ── Loading skeleton ────────────────────────────── --}}
            <div
                    wire:loading.flex wire:target="search, libraries, page"
                    class="grid grid-cols-5 gap-2 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10"
            >
                @foreach (range(1, 60) as $_)
                    <div class="aspect-square animate-pulse rounded-xl bg-zinc-100 dark:bg-zinc-800"></div>
                @endforeach
            </div>

            {{-- ── Icon grid ───────────────────────────────────── --}}
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
                                'group relative flex aspect-square flex-col items-center justify-center gap-1 rounded-xl border p-2 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-violet-500/40',
                                'border-violet-400/70 bg-violet-50 ring-2 ring-violet-500/30 dark:border-violet-600/50 dark:bg-violet-900/20' => $value === $icon,
                                'border-zinc-100 bg-white hover:-translate-y-0.5 hover:border-violet-300/70 hover:bg-violet-50/60 hover:shadow-sm dark:border-zinc-800 dark:bg-zinc-800/40 dark:hover:border-violet-700/50 dark:hover:bg-violet-900/10' => $value !== $icon,
                            ])
                    >
                        {{-- Selected badge --}}
                        @if ($value === $icon)
                            <span class="absolute right-0.5 top-0.5 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-violet-500">
                                <svg class="h-2 w-2 text-white" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        @endif

                        {{-- Icon SVG --}}
                        <span @class([
                            'flex h-5 w-5 items-center justify-center transition-colors duration-150',
                            'text-violet-600 dark:text-violet-400'  => $value === $icon,
                            'text-zinc-500 group-hover:text-violet-600 dark:text-zinc-400 dark:group-hover:text-violet-400' => $value !== $icon,
                        ])>
                            <x-dynamic-component :component="$icon" class="w-5 h-5"/>
                        </span>

                        {{-- Icon name — visible on sm+ --}}
                        <span class="hidden w-full truncate text-center font-mono text-[8px] leading-none tracking-tight text-zinc-400 group-hover:text-violet-500 dark:text-zinc-500 sm:block">
                            {{ Str::after($icon, '-') }}
                        </span>
                    </button>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center rounded-xl
                                border border-dashed border-zinc-200 py-14 text-center
                                dark:border-zinc-700">
                        <div class="mb-3 rounded-xl bg-zinc-100 p-3 dark:bg-zinc-800">
                            <svg class="h-6 w-6 text-zinc-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">
                            {{ __('tall-icon-picker::icon-picker.no_icons_found') }}
                        </p>
                        <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                            {{ __('tall-icon-picker::icon-picker.no_icons_hint') }}
                        </p>

                        @if ($search || count($libraries) > 1)
                            <button
                                    wire:click="resetFilters"
                                    type="button"
                                    class="mt-4 rounded-lg px-3 py-1.5 text-xs font-medium text-zinc-500
                                           transition-colors hover:bg-zinc-100 hover:text-zinc-700
                                           dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-300"
                            >
                                {{ __('tall-icon-picker::icon-picker.clear_filters') }}
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- ── Pagination ──────────────────────────────────── --}}
            @if ($this->icons()->lastPage() > 1)
                @php
                    $lastPage = $this->icons()->lastPage();
                    $start    = max(1, $page - 2);
                    $end      = min($lastPage, $page + 2);
                @endphp

                <div class="flex items-center justify-center gap-1 border-t border-zinc-100 pt-4 dark:border-zinc-800">

                    {{-- Prev --}}
                    <button wire:click="previousPage" type="button" @disabled($page <= 1)
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200
                                   text-zinc-400 transition-all hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-600
                                   disabled:pointer-events-none disabled:opacity-40
                                   dark:border-zinc-700 dark:text-zinc-500 dark:hover:bg-zinc-800">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    {{-- Mobile compact --}}
                    <span class="px-3 font-mono text-xs text-zinc-400 dark:text-zinc-500 sm:hidden">
                        {{ $page }}/{{ $lastPage }}
                    </span>

                    {{-- Desktop numbered --}}
                    @if ($start > 1)
                        <button wire:click="goToPage(1)" type="button"
                                class="hidden h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 font-mono text-xs
                                       text-zinc-500 transition-all hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800 sm:flex">
                            1
                        </button>
                        @if ($start > 2)
                            <span class="hidden px-1 font-mono text-xs text-zinc-300 dark:text-zinc-600 sm:block">…</span>
                        @endif
                    @endif

                    @for ($p = $start; $p <= $end; $p++)
                        <button wire:click="goToPage({{ $p }})" type="button"
                                @class([
                                    'hidden h-8 w-8 items-center justify-center rounded-lg border font-mono text-xs transition-all sm:flex',
                                    'border-violet-500 bg-violet-500 font-semibold text-white shadow-sm shadow-violet-500/30' => $p === $page,
                                    'border-zinc-200 text-zinc-500 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800' => $p !== $page,
                                ])
                        >{{ $p }}</button>
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span class="hidden px-1 font-mono text-xs text-zinc-300 dark:text-zinc-600 sm:block">…</span>
                        @endif
                        <button wire:click="goToPage({{ $lastPage }})" type="button"
                                class="hidden h-8 w-8 items-center justify-center rounded-lg border border-zinc-200 font-mono text-xs
                                       text-zinc-500 transition-all hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800 sm:flex">
                            {{ $lastPage }}
                        </button>
                    @endif

                    {{-- Next --}}
                    <button wire:click="nextPage" type="button" @disabled($page >= $this->icons()->lastPage())
                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-zinc-200
                                   text-zinc-400 transition-all hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-600
                                   disabled:pointer-events-none disabled:opacity-40
                                   dark:border-zinc-700 dark:text-zinc-500 dark:hover:bg-zinc-800">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                        class="rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm font-medium text-zinc-600
                               transition-all hover:bg-zinc-50 hover:border-zinc-300
                               focus:outline-none focus:ring-2 focus:ring-zinc-300/50 active:scale-[0.97]
                               dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300
                               dark:hover:border-zinc-600 dark:hover:bg-zinc-700"
                >
                    {{ __('tall-icon-picker::icon-picker.cancel') }}
                </button>
            </div>
        </x-slot:footer>

    </x-tall::ui.drawer>
</div>
