@props([
    'weatherStatus' => 'amber',
    'boatBookingDisabled' => false,
])

<section class="mx-auto max-w-6xl px-4 pb-12 sm:px-6 lg:px-8">
    <div class="bento-shell">
        <div class="bento-grid">
            <article class="bento-card lg:col-span-8">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="eyebrow">Unified Calendar</p>
                        <h2 class="section-title">Stay and sail reservations in one timeline</h2>
                    </div>
                    <div class="hidden gap-2 sm:flex">
                        <span class="pill pill-stay">Stay gradient</span>
                        <span class="pill pill-sail">Sail gradient</span>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-[1.2fr_0.8fr]">
                    <div class="warm-subpanel min-h-[280px]">
                        <div class="grid h-full grid-cols-7 gap-2 text-center text-xs sm:text-sm">
                            @for ($day = 1; $day <= 35; $day++)
                                <div class="border border-earth-espresso/8 bg-white/35 px-2 py-3 text-earth-espresso/75 {{ in_array($day, [9,10,11,12]) ? 'bg-gradient-to-r from-earth-ochre/75 to-earth-bronze/75 text-earth-espresso shadow-wood-glow' : '' }} {{ in_array($day, [18,19]) ? 'bg-gradient-to-r from-earth-walnut/80 to-earth-teak/70 text-earth-sand' : '' }}">
                                    <div class="text-[11px] font-semibold uppercase tracking-[0.14em]">{{ $day }}</div>
                                    <div class="mt-3 opacity-70">{{ in_array($day, [18,19]) ? 'Sail' : (in_array($day, [9,10,11,12]) ? 'Stay' : 'Open') }}</div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <aside class="space-y-4">
                        <div class="warm-subpanel">
                            <p class="eyebrow">Filters</p>
                            <div class="mt-4 space-y-3">
                                <div class="icon-stepper">
                                    <span>Adults</span>
                                    <div class="stepper-controls">
                                        <button type="button" aria-label="Decrease adults">-</button>
                                        <span>2</span>
                                        <button type="button" aria-label="Increase adults">+</button>
                                    </div>
                                </div>
                                <div class="icon-stepper">
                                    <span>Children</span>
                                    <div class="stepper-controls">
                                        <button type="button" aria-label="Decrease children">-</button>
                                        <span>1</span>
                                        <button type="button" aria-label="Increase children">+</button>
                                    </div>
                                </div>
                                <div class="icon-stepper">
                                    <span>Fuel add-on</span>
                                    <div class="stepper-controls">
                                        <button type="button" aria-label="Remove fuel add-on">-</button>
                                        <span>1</span>
                                        <button type="button" aria-label="Add fuel add-on">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="warm-subpanel">
                            <p class="eyebrow">Empty-state recovery</p>
                            <p class="mt-3 text-sm font-medium leading-7 text-earth-espresso/76">
                                No direct matches. Suggest nearby dates, shorter charters, or a different vessel class before the guest exits.
                            </p>
                        </div>
                    </aside>
                </div>
            </article>

            <article class="bento-card lg:col-span-4">
                <p class="eyebrow">Calamity / Weather</p>
                <h2 class="section-title">Global alert and sail safety state</h2>
                <div class="mt-6 space-y-4">
                    <div class="status-panel status-{{ strtolower($weatherStatus) }}">
                        <div>
                            <p class="text-xs uppercase tracking-[0.26em]">Current status</p>
                            <p class="mt-2 font-serif text-2xl">{{ strtoupper($weatherStatus) }}</p>
                        </div>
                        <p class="text-sm font-medium leading-7 opacity-80">
                            Red disables sail selection and swaps Reserve for Safety Advisory messaging.
                        </p>
                    </div>
                    <div class="warm-subpanel">
                        <p class="eyebrow">Guardrail</p>
                        <p class="mt-3 text-sm font-medium leading-7 text-earth-espresso/76">
                            Alert placement stays above logo and nav on every breakpoint. Never bury it in a drawer.
                        </p>
                    </div>
                </div>
            </article>

            <article class="bento-card lg:col-span-5">
                <p class="eyebrow">Inventory Cards</p>
                <h2 class="section-title">Hover-to-expand room and boat specs</h2>
                <div class="mt-6 grid gap-4">
                    <div class="expand-card">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-serif text-2xl font-bold text-earth-espresso">Beachfront Cabana</h3>
                                <p class="mt-2 text-sm font-medium text-earth-espresso/76">Sleeps 4. Breakfast included. Direct beach access.</p>
                            </div>
                            <span class="pill pill-stay">Stay</span>
                        </div>
                        <div class="expand-drawer">
                            <p class="font-medium">Drawer content: rate details, inclusions, cancellation, and cross-sell harbor tour suggestions.</p>
                        </div>
                    </div>
                    <div class="expand-card">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-serif text-2xl font-bold text-earth-espresso">Island Hopper 32</h3>
                                <p class="mt-2 text-sm font-medium text-earth-espresso/76">Good for half-day charters, snorkeling stops, and sunset sail-outs.</p>
                            </div>
                            <span class="pill pill-sail">Sail</span>
                        </div>
                        <div class="expand-drawer">
                            <p class="font-medium">
                                Drawer content: route map, fuel assumptions, capacity, and live disable state.
                                @if($boatBookingDisabled)
                                    Safety Advisory is active.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </article>

            <article class="bento-card lg:col-span-3">
                <p class="eyebrow">Revenue</p>
                <h2 class="section-title">High-signal admin stats</h2>
                <div class="mt-6 grid gap-4">
                    <div class="stat-chip group p-6 border border-gray-100 hover:border-[#964B00] transition-colors bg-white shadow-sm">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 block mb-2">Stay revenue</span>
                        <strong class="text-3xl font-bold text-gray-900 group-hover:text-[#964B00] transition-colors">
                            PHP <span class="stat-value" data-value="184" data-suffix="k">0</span>
                        </strong>
                    </div>
                    <div class="stat-chip group p-6 border border-gray-100 hover:border-[#964B00] transition-colors bg-white shadow-sm">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 block mb-2">Sail revenue</span>
                        <strong class="text-3xl font-bold text-gray-900 group-hover:text-[#964B00] transition-colors">
                            PHP <span class="stat-value" data-value="96" data-suffix="k">0</span>
                        </strong>
                    </div>
                </div>
            </article>

            <article class="bento-card lg:col-span-4">
                <p class="eyebrow">Pricing</p>
                <h2 class="section-title">Live total sidebar</h2>
                <div class="mt-6 warm-subpanel space-y-4 border border-gray-100 p-8 bg-gray-50">
                    <div class="flex items-center justify-between text-sm font-medium">
                        <span class="text-gray-500 uppercase tracking-widest text-[10px] font-bold">Cabana subtotal</span>
                        <span class="font-bold text-gray-900">PHP 14,400</span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-medium">
                        <span class="text-gray-500 uppercase tracking-widest text-[10px] font-bold">Boat charter</span>
                        <span class="font-bold text-gray-900">PHP 7,500</span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-medium">
                        <span class="text-gray-500 uppercase tracking-widest text-[10px] font-bold">Taxes + fuel</span>
                        <span class="font-bold text-gray-900">PHP 2,700</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 pt-6 font-bold text-[#964B00]">
                        <span class="uppercase tracking-[0.2em] text-xs">Total</span>
                        <span class="text-2xl">PHP 24,600</span>
                    </div>
                </div>
            </article>

            <article class="bento-card lg:col-span-3">
                <p class="eyebrow">Quick Actions</p>
                <h2 class="section-title">Frontdesk actions</h2>
                <div class="mt-6 grid gap-4">
                    <button type="button" class="w-full bg-[#964B00] text-white py-4 text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-[#7a3c00] transition shadow-lg shadow-orange-900/10">Add reservation</button>
                    <button type="button" class="w-full bg-white border border-gray-200 text-gray-700 py-4 text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-gray-50 transition">Check marina status</button>
                    <button type="button" class="w-full bg-white border border-gray-200 text-gray-700 py-4 text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-gray-50 transition">Review extensions</button>
                </div>
            </article>
        </div>
    </div>

    <div class="sticky-booking-bar mt-6 lg:hidden">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-earth-sand/70">Live total</p>
            <p class="font-serif text-2xl font-bold text-earth-parchment">PHP 24,600</p>
        </div>
        <button type="button" class="reserve-button" @if($boatBookingDisabled) title="Safety advisory is active." @endif>
            Reserve
        </button>
    </div>
</section>

