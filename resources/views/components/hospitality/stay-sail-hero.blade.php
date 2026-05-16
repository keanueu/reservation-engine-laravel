@props([
    'mode' => 'stay',
    'weatherStatus' => 'green',
    'boatBookingDisabled' => false,
    'stayCount' => 12,
    'sailCount' => 8,
])

@php
    $activeMode = strtolower($mode) === 'sail' ? 'sail' : 'stay';
    $sailDisabled = $boatBookingDisabled || strtolower($weatherStatus) === 'red';
@endphp

<section x-data="{ mode: '{{ $activeMode }}' }" class="mx-auto max-w-6xl px-4 pb-8 pt-6 sm:px-6 lg:px-8">
    <div class="warm-panel overflow-hidden p-4 sm:p-6 lg:p-8">
        <div class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
            <div class="space-y-6">
                <div class="inline-flex border border-white/15 bg-white/10 p-1 text-xs font-semibold  tracking-[0.18em] text-earth-sand">
                    <button
                        type="button"
                        @click="mode = 'stay'"
                        :class="mode === 'stay' ? 'bg-earth-ochre text-earth-espresso shadow-wood-glow' : 'text-earth-sand/80'"
                        class="border border-transparent px-4 py-2 transition"
                    >
                        Stay
                    </button>
                    <button
                        type="button"
                        @click="mode = 'sail'"
                        @if($sailDisabled) disabled @endif
                        :class="mode === 'sail' ? 'bg-earth-ochre text-earth-espresso shadow-wood-glow' : 'text-earth-sand/80'"
                        class="border border-transparent px-4 py-2 transition disabled:cursor-not-allowed disabled:opacity-40"
                        title="{{ $sailDisabled ? 'Safety advisory in effect. Boat bookings are disabled.' : 'Explore boats, yachts, and harbor charters.' }}"
                    >
                        Sail
                    </button>
                </div>

                <div class="space-y-4">
                    <p class="text-xs font-semibold  tracking-[0.22em] text-earth-ochre">Dual-engine booking</p>
                    <h1 class="max-w-3xl font-serif text-4xl font-extrabold leading-tight tracking-tight text-earth-parchment sm:text-5xl">
                        A single luxury funnel for cabanas, suites, charters, and weather-aware departures.
                    </h1>
                    <p class="max-w-2xl text-base font-medium leading-7 text-earth-sand/82">
                        Keep guests in one elegant flow. Stay inventory and sail inventory share totals, weather intelligence,
                        and cross-sell prompts without hiding the next action.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <article class="mini-bento">
                        <p class="text-xs font-semibold  tracking-[0.18em] text-earth-sand/70">Stay inventory</p>
                        <p class="mt-3 font-serif text-3xl font-bold text-earth-parchment">{{ $stayCount }}</p>
                        <p class="mt-2 text-sm font-medium text-earth-sand/74">Cabanas and guest rooms</p>
                    </article>
                    <article class="mini-bento">
                        <p class="text-xs font-semibold  tracking-[0.18em] text-earth-sand/70">Sail inventory</p>
                        <p class="mt-3 font-serif text-3xl font-bold text-earth-parchment">{{ $sailCount }}</p>
                        <p class="mt-2 text-sm font-medium text-earth-sand/74">Tours, yachts, and boats</p>
                    </article>
                    <article class="mini-bento">
                        <p class="text-xs font-semibold  tracking-[0.18em] text-earth-sand/70">Weather rule</p>
                        <p class="mt-3 font-serif text-3xl font-bold text-earth-parchment">{{ ucfirst($weatherStatus) }}</p>
                        <p class="mt-2 text-sm font-medium text-earth-sand/74">Red auto-disables sail checkout</p>
                    </article>
                </div>
            </div>

            <div class="grid gap-4">
                <article class="mini-bento">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold  tracking-[0.18em] text-earth-sand/70">Live total</p>
                            <h2 class="mt-3 font-serif text-3xl font-bold text-earth-parchment">PHP 24,600</h2>
                        </div>
                        <svg class="h-10 w-10 text-earth-ochre hover:animate-anchor-sway" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13m0-13a3 3 0 103 3m-3-3a3 3 0 11-3 3m3 10c-2.761 0-5 1.79-5 4m5-4c2.761 0 5 1.79 5 4m-9-7h8" />
                        </svg>
                    </div>
                    <p class="mt-3 text-sm font-medium text-earth-sand/74">
                        Sticky on mobile. Always visible with taxes, fuel, and reserve action.
                    </p>
                </article>

                <article class="mini-bento">
                    <p class="text-xs font-semibold  tracking-[0.18em] text-earth-sand/70">Contextual cross-sell</p>
                    <h3 class="mt-3 font-serif text-2xl font-bold text-earth-parchment">Harbor Sunset Cruise</h3>
                    <p class="mt-3 text-sm font-medium text-earth-sand/74">
                        Highlight this only when its departure window overlaps the selected stay dates.
                    </p>
                </article>

                <article class="mini-bento border border-dashed border-earth-sand/20">
                    <p class="text-xs font-semibold  tracking-[0.18em] text-earth-sand/70">Navigation rule</p>
                    <p class="mt-3 text-sm font-medium leading-7 text-earth-sand/82">
                        Search to results is one action. Results to details or calendar is the second. No modal-in-modal detours.
                    </p>
                </article>
            </div>
        </div>
    </div>
</section>


