@props(['placeholder' => '', 'icon' => null, 'label' => null, 'hint' => null])

@php
    $adapter = config('tall-icon-picker.ui', 'tallstackui');
@endphp

@if ($adapter === 'tallstackui')
    <x-ts-input
            :label="$label"
            :hint="$hint"
            :placeholder="$placeholder"
            :icon="$icon"
            {{ $attributes->whereStartsWith('wire:model') }}
    />
@else
    <div class="flex flex-col gap-1">
        @if ($label)
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
        @endif

        <div class="relative flex items-center">
            @if ($icon)
                <span class="pointer-events-none absolute left-3 flex items-center text-gray-400 dark:text-zinc-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
                    </svg>
                </span>
            @endif

            <input
                type="text"
                placeholder="{{ $placeholder }}"
                {{ $attributes->whereStartsWith('wire:model') }}
                @class([
                    'w-full rounded-lg border border-gray-200 bg-white py-2 text-sm text-gray-900 placeholder-gray-400 shadow-none transition-all duration-200',
                    'focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20',
                    'dark:border-zinc-700 dark:bg-zinc-800 dark:text-gray-200 dark:placeholder-zinc-500 dark:focus:border-blue-500',
                    'pl-9 pr-3' => $icon,
                    'px-3'      => ! $icon,
                ])
            >
        </div>

        @if ($hint)
            <span class="text-xs text-gray-500 dark:text-zinc-400">{{ $hint }}</span>
        @endif
    </div>
@endif
