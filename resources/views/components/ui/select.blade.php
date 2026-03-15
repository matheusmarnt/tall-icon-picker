@props([
    'label'      => null,
    'hint'       => null,
    'options'    => [],
    'select'     => 'label:label|value:value',
    'multiple'   => false,
    'searchable' => false,
])

@php
    $adapter = config('tall-icon-picker.ui', 'native');

    $selectMap = [];
    foreach (explode('|', $select) as $part) {
        $segments = explode(':', $part, 2);
        if (count($segments) === 2) {
            $selectMap[$segments[0]] = $segments[1];
        }
    }
    $labelKey = $selectMap['label'] ?? 'label';
    $valueKey = $selectMap['value'] ?? 'value';

    $alpineOptions = collect($options)
        ->map(fn ($opt) => ['label' => $opt[$labelKey] ?? '', 'value' => $opt[$valueKey] ?? ''])
        ->values()
        ->toJson(JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);

    $wireProperty = '';
    foreach ($attributes as $attrKey => $attrValue) {
        if (str_starts_with((string) $attrKey, 'wire:model')) {
            $wireProperty = $attrValue;
            break;
        }
    }
@endphp

@if ($adapter === 'tallstackui')
    <x-ts-select.styled
        :label="$label"
        :hint="$hint"
        :options="$options"
        :select="$select"
        :multiple="$multiple"
        :searchable="$searchable"
        {{ $attributes->whereStartsWith('wire:model') }}
    />
@else
    {{-- v1.3.0 reactive getter pattern — no entangle initialization-order risk --}}
    <div class="flex flex-col gap-1"
         data-options="{{ $alpineOptions }}"
         data-selected-text="{{ __('tall-icon-picker::icon-picker.selected') }}"
         data-placeholder-text="{{ __('tall-icon-picker::icon-picker.select_placeholder') }}"
         x-data="{
            open: false,
            search: '',
            options: JSON.parse($el.dataset.options),
            get selected() { return $wire.{{ $wireProperty }} || []; },
            selectedText: $el.dataset.selectedText,
            placeholderText: $el.dataset.placeholderText,
            get filtered() {
                if (!this.search) return this.options;
                const q = this.search.toLowerCase();
                return this.options.filter(o => o.label.toLowerCase().includes(q));
            },
            isSelected(val) { return this.selected.includes(val); },
            toggle(val) {
                const updated = this.selected.includes(val)
                    ? this.selected.filter(v => v !== val)
                    : [...this.selected, val];
                $wire.set('{{ $wireProperty }}', updated);
            },
            remove(val) {
                $wire.set('{{ $wireProperty }}', this.selected.filter(v => v !== val));
            },
            get triggerText() {
                if (this.selected.length > 0) return this.selected.length + ' ' + this.selectedText;
                return this.placeholderText;
            }
         }"
         @click.away="open = false"
         @keydown.escape="open = false"
    >
        @if ($label)
            <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $label }}</label>
        @endif

        <div class="relative">
            <button
                type="button"
                @click="open = !open"
                class="flex w-full items-center justify-between rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm transition-all
                       hover:border-zinc-300 focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-500/20
                       dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-zinc-600"
            >
                <span class="truncate text-zinc-600 dark:text-zinc-300" x-text="triggerText"></span>
                <svg class="h-4 w-4 shrink-0 text-zinc-400 transition-transform duration-200"
                     :class="open ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                x-cloak
                class="absolute z-50 mt-1 w-full rounded-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-800"
            >
                @if ($searchable)
                    <div class="border-b border-zinc-100 p-2 dark:border-zinc-700">
                        <input x-model="search" type="text"
                               placeholder="{{ __('tall-icon-picker::icon-picker.search_placeholder') }}"
                               class="w-full rounded-md border border-zinc-200 bg-zinc-50 px-3 py-1.5 text-sm
                                      focus:border-violet-400 focus:outline-none focus:ring-1 focus:ring-violet-500/20
                                      dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-100">
                    </div>
                @endif

                <div class="max-h-56 overflow-y-auto p-1">
                    <template x-for="option in filtered" :key="option.value">
                        <button type="button" @click="toggle(option.value)"
                                :class="isSelected(option.value)
                                    ? 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-300'
                                    : 'text-zinc-700 hover:bg-zinc-50 dark:text-zinc-300 dark:hover:bg-zinc-700'"
                                class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm transition-colors">
                            <span class="flex h-4 w-4 shrink-0 items-center justify-center">
                                <template x-if="isSelected(option.value)">
                                    <svg class="h-3.5 w-3.5 text-violet-600 dark:text-violet-400"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                            </span>
                            <span x-text="option.label"></span>
                        </button>
                    </template>

                    <template x-if="filtered.length === 0">
                        <p class="px-3 py-2 text-sm text-zinc-400 dark:text-zinc-500">
                            {{ __('tall-icon-picker::icon-picker.no_icons_found') }}
                        </p>
                    </template>
                </div>
            </div>
        </div>

        @if ($hint)
            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</span>
        @endif
    </div>
@endif
