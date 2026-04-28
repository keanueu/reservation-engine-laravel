@props(['discount'])

@php
  $images = $discount->images ?? collect();
  $images = $images->sortBy('sort_order')->values();
@endphp

<div class=" overflow-hidden bg-white dark:bg-gray-800 shadow">
  @if($images->isNotEmpty())
    <div x-data="{ idx: 0 }" class="relative">
      <div class="h-40 md:h-56 overflow-hidden">
        @foreach($images as $i => $img)
          <img x-show="idx === {{ $i }}" x-cloak
               src="{{ asset('images/promotions/' . $img->filename) }}"
               alt="{{ $img->alt ?? $discount->name }}"
               class="w-full h-40 md:h-56 object-cover transition-opacity duration-300">
        @endforeach
      </div>

      {{-- prev/next --}}
      <button @click="idx = idx === 0 ? {{ $images->count()-1 }} : idx - 1"
              class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black/30 text-white px-2 py-1 rounded">‹</button>
      <button @click="idx = idx === {{ $images->count()-1 }} ? 0 : idx + 1"
              class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black/30 text-white px-2 py-1 rounded">›</button>

      {{-- indicators --}}
      <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2">
        @foreach($images as $i => $img)
          <button @click="idx = {{ $i }}" :class="idx === {{ $i }} ? 'bg-white' : 'bg-gray-400'" class="w-2 h-2 "></button>
        @endforeach
      </div>
    </div>
  @else
    <div class="h-40 md:h-56 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
      <span class="text-sm text-gray-500">No promotion images</span>
    </div>
  @endif

  <div class="p-3">
    <div class="text-sm font-semibold text-gray-800 dark:text-white">{{ $discount->name }}</div>
    <div class="text-xs text-gray-500">
      @if($discount->amount_type === 'percent')
        {{ rtrim(rtrim(number_format($discount->amount,2),'0'),'.') }}% off
      @else
        ₱{{ number_format($discount->amount, 2) }} off
      @endif
    </div>
    @if($discount->start_date || $discount->end_date)
      <div class="text-xs text-gray-400 mt-1">
        {{ $discount->start_date ? $discount->start_date : '—' }} to {{ $discount->end_date ? $discount->end_date : '—' }}
      </div>
    @endif
  </div>
</div>
