<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-6 py-3 bg-white border border-gray-200 font-bold text-[11px] text-gray-700  tracking-[0.2em] shadow-sm hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 disabled:opacity-25 transition-all duration-300']) }}>
    {{ $slot }}
</button>

