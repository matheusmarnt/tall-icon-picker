@props([
    'label'      => null,
    'options'    => [],
    'select'     => 'label:label|value:value',
    'multiple'   => false,
    'searchable' => false,
])

@php
    $adapter = config('tall-icon-picker.ui', 'native');

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

    // Build the options array Alpine will consume
    $alpineOptions = collect($options)
        ->map(fn ($opt) => ['label' => $opt[$labelKey] ?? '', 'value' => $opt[$valueKey] ?? ''])
        ->values()
        ->toJson(JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    // Extract the Livewire property name from wire:model* attribute
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
        :options="$options"
        :select="$select"
        :multiple="$multiple"
        :searchable="$searchable"
        {{ $attributes->whereStartsWith('wire:model') }}
    />
@else
    <div
        data-options="{{ $alpineOptions }}"
        x-data="{
            open: false,
            search: '',
            options: JSON.parse($el.dataset.options),
            get selected() { return $wire.{{ $wireProperty }} || []; },
            get filtered() {
                if (!this.search) return this.options;
                const q = this.search.toLowerCase();
                return this.options.filter(o => o.label.toLowerCase().includes(q));
            },
            toggle(val) {
                const current = this.selected;
                const updated = current.includes(val)
                    ? current.filter(v => v !== val)
                    : [...current, val];
                $wire.set('{{ $wireProperty }}', updated);
            },
            remove(val) {
                $wire.set('{{ $wireProperty }}', this.selected.filter(v => v !== val));
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
        @if ($label)
            <label class="py-1.5 block text-sm font-medium text-zinc-300">
                {{ $label }}
            </label>
        @endif

        {{-- Trigger button --}}
        <button
            type="button"
            @click="open = !open"
            class="flex min-h-[40px] w-full flex-wrap items-center gap-1.5 rounded-xl
                   border border-zinc-700 bg-zinc-800/50 px-3 py-2 text-left text-sm
                   transition-all duration-200
                   focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500"
        >
            <template x-if="selected.length === 0">
                <span class="text-zinc-500">
                    {{ __('tall-icon-picker::icon-picker.no_icon_selected') }}
                </span>
            </template>

            <template x-for="val in selected" :key="val">
                <span class="inline-flex items-center gap-1 rounded-full
                             bg-indigo-500/15 px-2 py-0.5 text-xs text-indigo-300">
                    <span x-text="labelFor(val)"></span>
                    <button
                        type="button"
                        @click.stop="remove(val)"
                        class="ml-0.5 text-indigo-400 transition-colors hover:text-indigo-200"
                    >
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </span>
            </template>

            <span class="ml-auto shrink-0 text-zinc-500 transition-transform duration-200"
                  :class="{ 'rotate-180': open }">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </span>
        </button>

        {{-- Dropdown --}}
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
                   border border-zinc-700 bg-zinc-900/95 backdrop-blur-xl
                   shadow-2xl shadow-black/30"
        >
            @if ($searchable)
                <div class="border-b border-zinc-800 p-2">
                    <input
                        type="text"
                        x-model="search"
                        placeholder="{{ __('tall-icon-picker::icon-picker.search_placeholder') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-800
                               px-3 py-1.5 text-sm text-zinc-100 placeholder-zinc-500
                               focus:outline-none focus:ring-1 focus:ring-indigo-500/50"
                    />
                </div>
            @endif

            <div class="max-h-64 overflow-y-auto p-1">
                <template x-for="option in filtered" :key="option.value">
                    <button
                        type="button"
                        @click="toggle(option.value)"
                        :class="isSelected(option.value)
                            ? 'text-indigo-300 bg-indigo-500/10'
                            : 'text-zinc-300'"
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2
                               text-left text-sm transition-colors hover:bg-indigo-500/15"
                    >
                        <span class="flex h-4 w-4 shrink-0 items-center justify-center">
                            <template x-if="isSelected(option.value)">
                                <svg class="h-3.5 w-3.5 text-indigo-400" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                        </span>
                        <span x-text="option.label"></span>
                    </button>
                </template>

                <template x-if="filtered.length === 0">
                    <p class="px-3 py-2 text-sm text-zinc-500">
                        {{ __('tall-icon-picker::icon-picker.no_icons_found') }}
                    </p>
                </template>
            </div>
        </div>
    </div>
@endif
