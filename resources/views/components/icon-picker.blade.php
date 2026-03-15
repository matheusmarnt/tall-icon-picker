@props(['label' => null, 'hint' => null, 'placeholder' => null, 'modelName' => null])

@php
    $modelAttribute = $attributes->whereStartsWith('wire:model')->first();
    $resolvedModel  = $modelName ?? $attributes->get('wire:model') ?? $attributes->get('wire:model.live') ?? '';
    $hasError       = isset($errors) && $errors->has($resolvedModel);
    $errorMessage   = $hasError ? $errors->first($resolvedModel) : null;
@endphp

<div class="flex flex-col gap-1.5">
    @if ($label)
        <label @class([
            'block text-sm font-medium transition-colors duration-150',
            'text-red-600 dark:text-red-400'   => $hasError,
            'text-gray-700 dark:text-gray-300' => ! $hasError,
        ])>
            {{ $label }}
        </label>
    @endif

    <div @class([
        'rounded-xl transition-all duration-200',
        'ring-2 ring-red-500/40 ring-offset-0' => $hasError,
    ])>
        <livewire:tall.icon-picker
            :parent-model="$resolvedModel"
            :placeholder="$placeholder"
            {{ $attributes->whereStartsWith('wire:model') }}
        />
    </div>

    @if ($errorMessage)
        <div role="alert" class="flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400">
            <svg aria-hidden="true" class="h-3.5 w-3.5 shrink-0" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            {{ $errorMessage }}
        </div>
    @elseif ($hint)
        <p class="flex items-center gap-1.5 text-sm text-gray-400 dark:text-zinc-500">
            <svg aria-hidden="true" class="h-3.5 w-3.5 shrink-0" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
            {{ $hint }}
        </p>
    @endif
</div>
