@props(['property' => 'open', 'title' => '', 'size' => '5xl'])

@php
    $adapter = config('tall-icon-picker.ui', 'native');
    $maxWidth = match($size) {
        'sm' => 'sm:max-w-sm', 'md' => 'sm:max-w-md', 'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl', '2xl' => 'sm:max-w-2xl', '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl', '5xl' => 'sm:max-w-5xl', '6xl' => 'sm:max-w-6xl',
        'full' => 'sm:max-w-full', default => 'sm:max-w-5xl',
    };
@endphp

@if ($adapter === 'tallstackui')
    <x-ts-slide wire="{{ $property }}" :title="$title" :size="$size">
        {{ $slot }}
        @if (isset($footer)) <x-slot:footer>{{ $footer }}</x-slot:footer> @endif
    </x-ts-slide>
@else
    <div x-data="{ open: $wire.entangle('{{ $property }}') }" x-cloak @keydown.escape.window="open = false" class="relative z-50">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm dark:bg-zinc-900/80"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="open"
                         x-transition:enter="transform transition ease-in-out duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         @click.outside="open = false"
                         class="pointer-events-auto w-screen {{ $maxWidth }}">

                        <div class="flex h-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                            {{-- Header --}}
                            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-zinc-800">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h2>
                                <button @click="open = false" class="rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:hover:bg-zinc-800 dark:hover:text-gray-300">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="relative flex-1 overflow-y-auto px-6 py-5">
                                {{ $slot }}
                            </div>

                            {{-- Footer --}}
                            @if (isset($footer))
                                <div class="border-t border-gray-200 px-6 py-4 dark:border-zinc-800">
                                    {{ $footer }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
