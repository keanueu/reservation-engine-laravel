@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-[10px] uppercase tracking-widest text-gray-500 mb-1']) }}>
    {{ $value ?? $slot }}
</label>
