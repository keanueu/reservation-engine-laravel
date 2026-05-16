@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'bg-red-50 border-l-4 border-red-600 p-6 shadow-sm mb-6']) }}>
        <div class="font-bold text-[10px] text-red-600 mb-2">{{ __('Error details') }}</div>

        <ul class="list-none text-xs font-bold text-red-700/80 space-y-1">
            @foreach ($errors->all() as $error)
                <li class="flex items-center gap-2">
                    <span class="h-1 w-1 bg-red-600"></span>
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif

