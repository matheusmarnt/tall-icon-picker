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
        $base  = 'inline-flex items-center gap-1.5 font-medium rounded-lg transition-all duration-150';
        $base .= ' focus:outline-none focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-zinc-900';
        $base .= ' active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed';

        $size = $sm ? 'px-3 py-1.5 text-sm' : 'px-4 py-2 text-sm';

        if ($outline) {
            $style = 'bg-transparent border border-zinc-300 dark:border-zinc-600';
            $style .= ' text-zinc-600 dark:text-zinc-300';
            $style .= ' hover:border-zinc-400 hover:bg-zinc-50 dark:hover:border-zinc-500 dark:hover:bg-zinc-800';
            $style .= ' focus:ring-zinc-400';
        } elseif ($color === 'secondary') {
            $style  = 'border border-zinc-200 dark:border-zinc-700';
            $style .= ' bg-white dark:bg-zinc-800';
            $style .= ' text-zinc-700 dark:text-zinc-300';
            $style .= ' hover:bg-zinc-50 hover:border-zinc-300 dark:hover:bg-zinc-700 dark:hover:border-zinc-600';
            $style .= ' focus:ring-zinc-400';
        } else {
            $style  = 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white border border-transparent';
            $style .= ' shadow-sm shadow-indigo-500/20 hover:shadow-md hover:shadow-indigo-500/30';
            $style .= ' hover:from-indigo-500 hover:to-violet-500 focus:ring-indigo-500';
        }
    @endphp

    <button
        type="{{ $type }}"
        {{ $attributes->class([$base, $size, $style]) }}
    >
        @if ($icon === 'magnifying-glass')
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
            </svg>
        @endif

        {{ $slot }}
    </button>
@endif
