<div class="md:col-span-1 flex justify-between">
    <div class="px-4 sm:px-0">
        <h3 class="text-xs font-bold  tracking-[0.3em] text-[#964B00] mb-2">{{ $title }}</h3>

        <p class="mt-1 text-sm text-gray-500 leading-relaxed max-w-xs">
            {{ $description }}
        </p>
    </div>

    <div class="px-4 sm:px-0">
        {{ $aside ?? '' }}
    </div>
</div>

