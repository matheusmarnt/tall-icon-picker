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
        $base .= ' focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900';
        $base .= ' active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed';

        $size = $sm ? 'px-3 py-1.5 text-sm' : 'px-4 py-2 text-sm';

        if ($outline) {
            $style = 'bg-transparent border border-indigo-500/40 text-indigo-400 hover:bg-indigo-500/10 focus:ring-indigo-500';
        } elseif ($color === 'secondary') {
            $style = 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 focus:ring-zinc-500';
        } else {
            $style  = 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white';
            $style .= ' shadow-md shadow-indigo-500/25 hover:shadow-lg hover:shadow-indigo-500/40';
            $style .= ' hover:scale-[1.02] focus:ring-indigo-500';
        }
    @endphp

    <button
        type="{{ $type }}"
        {{ $attributes->class([$base, $size, $style]) }}
    >
        @if ($icon === 'magnifying-glass')
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.607 0 7.5 7.5 0 0010.607 0z"/>
            </svg>
        @endif

        {{ $slot }}
    </button>
@endif
