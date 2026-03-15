@props([
    'color'   => 'primary',
    'variant' => 'solid',
    'sm'      => false,
    'outline' => false,
    'icon'    => null,
    'type'    => 'button',
])

@php
    $adapter = config('tall-icon-picker.ui', 'tallstackui');
@endphp

@if ($adapter === 'tallstackui')
    <x-ts-button
            :color="$color"
            :variant="$variant"
            :sm="$sm"
            :outline="$outline"
            :icon="$icon"
            :type="$type"
            {{ $attributes }}
    >{{ $slot }}</x-ts-button>
@else
    @php
        $base      = 'inline-flex items-center justify-center gap-1.5 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-zinc-900 active:scale-95 disabled:pointer-events-none disabled:opacity-50';
        $sizeClass = $sm ? 'px-3 py-1.5 text-xs' : 'px-4 py-2 text-sm';

        if ($variant === 'flat' || $outline) {
            $style = $color === 'secondary'
                ? 'bg-transparent text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-200 focus:ring-gray-300'
                : 'bg-transparent text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 focus:ring-blue-500';
            if ($outline) {
                $style .= ' border border-current';
            }
        } else {
            $style = $color === 'secondary'
                ? 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 focus:ring-gray-200 shadow-sm'
                : 'bg-blue-500 text-white shadow-sm hover:bg-blue-600 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-500';
        }
    @endphp

    <button type="{{ $type }}" {{ $attributes->merge(['class' => "$base $sizeClass $style"]) }}>
        @if ($icon === 'magnifying-glass')
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
