<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-[#964B00] border border-transparent font-bold text-[11px] text-white  tracking-[0.2em] hover:bg-[#7a3c00] focus:bg-[#7a3c00] active:bg-[#63360D] focus:outline-none focus:ring-4 focus:ring-[#964B00]/20 disabled:opacity-50 transition-all duration-300 shadow-lg shadow-[#964B00]/10']) }}>
    {{ $slot }}
</button>

