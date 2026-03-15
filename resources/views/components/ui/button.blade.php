@props([
    'color'   => 'primary',
    'sm'      => false,
    'outline' => false,
    'icon'    => null,
    'type'    => 'button',
])

@php
    $adapter = config('tall-icon-picker.ui', 'native');
@endphp

@if ($adapter === 'tallstackui')
    <x-ts-button
            :color="$color"
            :sm="$sm"
            :outline="$outline"
            :icon="$icon"
            :type="$type"
            {{ $attributes }}
    >{{ $slot }}</x-ts-button>
@else
    @php
        $base  = 'inline-flex items-center justify-center gap-1.5 whitespace-nowrap font-medium rounded-lg';
        $base .= ' transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-1';
        $base .= ' dark:focus:ring-offset-zinc-900 active:scale-[0.97] disabled:pointer-events-none disabled:opacity-50';

        $size = $sm ? 'px-2.5 py-1.5 text-xs' : 'px-4 py-2 text-sm';

        if ($outline) {
            $style = 'border border-zinc-300 bg-transparent text-zinc-600 hover:border-zinc-400 hover:bg-zinc-50 focus:ring-zinc-300';
            $style .= ' dark:border-zinc-600 dark:text-zinc-300 dark:hover:border-zinc-500 dark:hover:bg-zinc-800';
        } elseif ($color === 'secondary') {
            $style = 'border border-zinc-200 bg-white text-zinc-700 shadow-sm hover:bg-zinc-50 hover:border-zinc-300 focus:ring-zinc-200';
            $style .= ' dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 dark:hover:border-zinc-600';
        } else {
            $style = 'bg-violet-600 text-white shadow-sm shadow-violet-500/20 hover:bg-violet-500 focus:ring-violet-500';
            $style .= ' dark:bg-violet-600 dark:hover:bg-violet-500';
        }
    @endphp

    <button type="{{ $type }}" {{ $attributes->class([$base, $size, $style]) }}>
        @if ($icon === 'magnifying-glass')
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
