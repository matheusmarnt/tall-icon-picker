@props([
    'label'      => null,
    'hint'       => null,
    'options'    => [],
    'select'     => 'label:label|value:value',
    'multiple'   => false,
    'searchable' => false,
])

@php
    $adapter = config('tall-icon-picker.ui', 'tallstackui');

    // Parse select prop: "label:name|value:id" → $labelKey = 'name', $valueKey = 'id'
    $selectMap = [];
    foreach (explode('|', $select) as $part) {
        $segments = explode(':', $part, 2);
        if (count($segments) === 2) {
            $selectMap[$segments[0]] = $segments[1];
        }
    }
    $labelKey = $selectMap['label'] ?? 'label';
    $valueKey = $selectMap['value'] ?? 'value';

    // {{ $alpineOptions }} (htmlspecialchars) encodes " as &quot;, which the browser
    // decodes back to " before JSON.parse() runs — safe in any HTML attribute context.
    $alpineOptions = collect($options)
        ->map(fn ($opt) => ['label' => $opt[$labelKey] ?? '', 'value' => $opt[$valueKey] ?? ''])
        ->values()
        ->toJson(JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);

    // Wire property names are PHP identifiers — no characters that need escaping.
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
    {{-- Options JSON and locale strings live in data-* attributes rendered via {{ }}   --}}
    {{-- (htmlspecialchars), making them safe in any HTML attribute quote style.         --}}
    {{-- The x-data string contains zero Blade-interpolated JSON or locale content,     --}}
    {{-- so it is safe in both single-quoted and double-quoted HTML attributes.          --}}
    {{-- Alpine logic follows the v1.3.0 pattern: direct $wire.property reactive getter --}}
    {{-- and explicit $wire.set() writes — no entangle, no initialization-order risk.   --}}
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
                if (this.selected.length > 0) {
                    return this.selected.length + ' ' + this.selectedText;
                }
                return this.placeholderText;
            }
         }"
         @click.away="open = false"
         @keydown.escape="open = false"
    >
        @if ($label)
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
        @endif

        <div class="relative">
            <button
                type="button"
                @click="open = !open"
                class="flex w-full items-center justify-between rounded-lg border border-gray-200 bg-white
                       px-3 py-2 text-sm text-gray-700 transition-all duration-200
                       focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20
                       dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-200"
            >
                <span class="truncate" x-text="triggerText"></span>
                <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200"
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
                class="absolute z-50 mt-1 w-full overflow-hidden rounded-lg border border-gray-200
                       bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-800"
            >
                @if ($searchable)
                    <div class="border-b border-gray-100 p-2 dark:border-zinc-700">
                        <input
                            x-model="search"
                            type="text"
                            placeholder="{{ __('tall-icon-picker::icon-picker.search_placeholder') }}"
                            class="w-full rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm
                                   focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500/20
                                   dark:border-zinc-600 dark:bg-zinc-900 dark:text-gray-200"
                        >
                    </div>
                @endif

                <div class="max-h-56 overflow-y-auto p-1">
                    <template x-for="option in filtered" :key="option.value">
                        <button
                            type="button"
                            @click="toggle(option.value)"
                            :class="isSelected(option.value)
                                ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400'
                                : 'text-gray-700 hover:bg-gray-50 dark:text-zinc-300 dark:hover:bg-zinc-700'"
                            class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-sm transition-colors"
                        >
                            <span class="flex h-4 w-4 shrink-0 items-center justify-center">
                                <template x-if="isSelected(option.value)">
                                    <svg class="h-3.5 w-3.5 text-blue-600 dark:text-blue-400" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
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

        @if ($hint)
            <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $hint }}</span>
        @endif
    </div>
@endif
