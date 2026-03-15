@props(['label' => null, 'modelName' => null])

@php
    $modelAttribute = $attributes->whereStartsWith('wire:model')->first();
    $resolvedModel  = $modelName ?? $attributes->get('wire:model') ?? $attributes->get('wire:model.live') ?? '';
@endphp

<div>
    @if ($label)
        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <livewire:tall.icon-picker
        :parent-model="$resolvedModel"
        {{ $attributes->whereStartsWith('wire:model') }}
    />
</div>