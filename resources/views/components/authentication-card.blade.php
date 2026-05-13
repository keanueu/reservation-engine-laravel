<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50">
    <div class="animate-fade-up">
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-10 py-12 bg-white shadow-2xl border border-gray-100 animate-fade-up" style="animation-delay: 0.1s">
        {{ $slot }}
    </div>
</div>
