@props(['placeholder' => '', 'icon' => null])

@php
    $adapter = config('tall-icon-picker.ui', 'native');
@endphp

@if ($adapter === 'tallstackui')
    <x-ts-input
        :placeholder="$placeholder"
        :icon="$icon"
        {{ $attributes->whereStartsWith('wire:model') }}
    />
@else
    <div class="relative flex items-center">
        @if ($icon)
            <span class="pointer-events-none absolute left-3 text-zinc-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
                </svg>
            </span>
        @endif

        <input
            type="text"
            placeholder="{{ $placeholder }}"
            {{ $attributes->whereStartsWith('wire:model') }}
            @class([
                'w-full rounded-xl border border-zinc-700 bg-zinc-800/50 py-2.5 pr-4 text-sm text-zinc-100',
                'placeholder-zinc-500 transition-all duration-200',
                'focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500',
                'pl-9' => (bool) $icon,
                'pl-4' => ! $icon,
            ])
        />
    </div>
@endif
