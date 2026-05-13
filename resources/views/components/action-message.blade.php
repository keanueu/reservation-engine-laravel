@props(['on'])

<div x-data="{ shown: false, timeout: null }"
    x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
    x-show="shown"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-2"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-1000"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;"
    {{ $attributes->merge(['class' => 'text-[10px] font-bold uppercase tracking-widest text-[#964B00] flex items-center gap-2']) }}>
    <span class="h-1 w-1 bg-[#964B00] animate-pulse"></span>
    {{ $slot->isEmpty() ? 'Changes Saved.' : $slot }}
</div>
