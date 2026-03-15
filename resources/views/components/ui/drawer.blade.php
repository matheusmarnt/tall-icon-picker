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
            class="relative z-50"
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
                class="fixed inset-0 z-40 bg-zinc-950/40 backdrop-blur-sm"
        ></div>

        {{-- Panel --}}
        <div
                x-show="open"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 z-50 flex w-full flex-col
                       border-l border-zinc-200 bg-white shadow-2xl shadow-zinc-950/10
                       dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-zinc-950/50
                       sm:max-w-xl md:max-w-3xl lg:max-w-5xl"
        >
            {{-- Header --}}
            <div class="flex shrink-0 items-center justify-between border-b border-zinc-100 px-5 py-3.5 dark:border-zinc-800">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-6 w-6 items-center justify-center rounded-md bg-violet-100 dark:bg-violet-900/30">
                        <svg class="h-3.5 w-3.5 text-violet-600 dark:text-violet-400" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zm0 9.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zm9.75-9.75A2.25 2.25 0 0115.75 3.75H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zm0 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                        </svg>
                    </div>
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $title }}</h2>
                </div>

                <button
                        @click="open = false"
                        type="button"
                        aria-label="{{ __('tall-icon-picker::icon-picker.cancel') }}"
                        class="rounded-lg p-1.5 text-zinc-400 transition-colors
                               hover:bg-zinc-100 hover:text-zinc-600
                               dark:hover:bg-zinc-800 dark:hover:text-zinc-300
                               focus:outline-none focus:ring-2 focus:ring-violet-500/40"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @if (isset($footer))
                <div class="shrink-0 border-t border-zinc-100 px-5 py-3 dark:border-zinc-800">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
@endif
