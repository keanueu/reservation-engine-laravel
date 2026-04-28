@props([
    'status' => 'green',
    'title' => 'Marina and resort operations are normal.',
    'message' => 'Live weather and calamity advisories update this bar first so guests see booking impacts before entering the funnel.',
    'updatedAt' => null,
    'boatDisabled' => false,
])

@php
    $toneMap = [
        'green' => 'border-calamity-green/30 bg-calamity-green/15 text-earth-espresso',
        'amber' => 'border-calamity-amber/30 bg-calamity-amber/15 text-earth-espresso',
        'red' => 'border-calamity-red/30 bg-calamity-red/15 text-white',
    ];

    $badgeMap = [
        'green' => 'bg-calamity-green text-white',
        'amber' => 'bg-calamity-amber text-earth-espresso',
        'red' => 'bg-calamity-red text-white',
    ];

    $normalizedStatus = strtolower($status);
@endphp

<section
    class="relative z-50 border-b px-4 py-3 sm:px-6 {{ $toneMap[$normalizedStatus] ?? $toneMap['green'] }}"
    role="status"
    aria-live="polite"
>
    <div class="mx-auto flex max-w-6xl flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 inline-flex border border-current/10 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] {{ $badgeMap[$normalizedStatus] ?? $badgeMap['green'] }}">
                {{ strtoupper($normalizedStatus) }}
            </span>
            <div class="space-y-1">
                <p class="font-serif text-base font-semibold sm:text-lg">{{ $title }}</p>
                <p class="max-w-3xl text-sm font-medium opacity-80">{{ $message }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm">
            @if($updatedAt)
                <span class="border border-current/15 px-3 py-1 font-medium">Updated {{ $updatedAt }}</span>
            @endif

            @if($boatDisabled)
                <span class="border border-current/15 px-3 py-1 font-medium">
                    Safety Advisory: Sail bookings are temporarily disabled.
                </span>
            @endif
        </div>
    </div>
</section>

