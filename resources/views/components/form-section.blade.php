@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-6']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="px-8 py-10 bg-white shadow-2xl border border-gray-100">
                <div class="grid grid-cols-6 gap-8">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-end px-8 py-6 bg-gray-50 border-x border-b border-gray-100 text-end shadow-2xl">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
