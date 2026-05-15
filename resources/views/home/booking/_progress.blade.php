@php $steps = ['Dates', 'Guests', 'Review']; @endphp
<div class="flex items-center mb-10">
    @foreach($steps as $i => $label)
        @php $num = $i + 1; @endphp
        <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-7 h-7 flex items-center justify-center text-sm font-medium transition-all"
                     style="{{ $current >= $num ? 'background:#964B00;color:#fff;' : 'background:#e5e7eb;color:#6b7280;' }}">
                    @if($current > $num)
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        {{ $num }}
                    @endif
                </div>
                <span class="text-sm font-medium hidden sm:block"
                      style="{{ $current >= $num ? 'color:#964B00;' : 'color:#9ca3af;' }}">{{ $label }}</span>
            </div>
            @if(!$loop->last)
                <div class="flex-1 h-px mx-3 transition-all"
                     style="{{ $current > $num ? 'background:#964B00;' : 'background:#e5e7eb;' }}"></div>
            @endif
        </div>
    @endforeach
</div>
