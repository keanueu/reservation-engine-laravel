<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-red-600 border border-transparent font-bold text-[11px] text-white uppercase tracking-[0.2em] hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-500/20 disabled:opacity-50 transition-all duration-300 shadow-lg shadow-red-600/10']) }}>
    {{ $slot }}
</button>
