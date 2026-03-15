@props(['property' => 'open', 'title' => '', 'size' => '6xl'])

@php
    $adapter = config('tall-icon-picker.ui', 'native');
@endphp

@if ($adapter === 'tallstackui')
    <x-ts-slide wire="{{ $property }}" :title="$title" :size="$size">
        {{ $slot }}
        @if (isset($footer))
            <x-slot:footer>{{ $footer }}</x-slot:footer>
        @endif
    </x-ts-slide>
@else
    <div
        x-data="{ open: $wire.entangle('{{ $property }}') }"
        x-cloak
        @keydown.escape.window="open = false"
        class="overflow-hidden"
    >
        {{-- Backdrop --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="open = false"
            class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
        ></div>

        {{-- Panel --}}
        <div
            x-show="open"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-250"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 z-50 flex w-full flex-col
                   bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl
                   border-l border-zinc-200 dark:border-zinc-800
                   shadow-2xl shadow-black/20
                   sm:max-w-xl md:max-w-3xl lg:max-w-5xl"
        >
            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between
                        border-b border-zinc-200 dark:border-zinc-800 px-5 py-4">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $title }}
                </h2>
                <button
                    @click="open = false"
                    type="button"
                    aria-label="{{ __('tall-icon-picker::icon-picker.cancel') }}"
                    class="rounded-lg p-1.5 text-zinc-400 transition-colors
                           hover:bg-zinc-100 hover:text-zinc-700
                           dark:hover:bg-zinc-800 dark:hover:text-zinc-300
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto px-5 py-4">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @if (isset($footer))
                <div class="shrink-0 border-t border-zinc-200 dark:border-zinc-800 px-5 py-4">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
@endif
