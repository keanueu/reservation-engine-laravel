# Warm Earth Hospitality Revamp

## Tailwind Theme Tokens

Use the extended `earth` palette and `calamity` statuses in [tailwind.config.js](C:\xampp\htdocs\UPDATED SYSTEM\tailwind.config.js).

- `earth.espresso`: primary headers, high-contrast text, and structural dividers.
- `earth.teak` / `earth.walnut`: borders, chart accents, and warm glass overlays.
- `earth.ochre`: primary CTA, focus rings, and active toggle state.
- `earth.sand` / `earth.parchment`: backgrounds, soft fills, and mobile card surfaces.
- `calamity.green`, `calamity.amber`, `calamity.red`: global notification states and booking lock rules.

Recommended font pairing:

- Brand and luxury headlines: `font-serif` (`Lora`).
- Marketing hero emphasis: `font-display` (`Playfair Display` fallback).
- UI, forms, dashboards: `font-montserrat` or `font-sans`.

## Blade / React Component Structure

This repo is Blade-first, so the primary implementation path should be Blade components with Alpine for state. A React island can mirror the same contract if you later move the booking funnel into `resources/js`.

Blade structure added:

- [global-calamity-banner.blade.php](C:\xampp\htdocs\UPDATED SYSTEM\resources\views\components\hospitality\global-calamity-banner.blade.php)
- [stay-sail-hero.blade.php](C:\xampp\htdocs\UPDATED SYSTEM\resources\views\components\hospitality\stay-sail-hero.blade.php)
- [bento-dashboard.blade.php](C:\xampp\htdocs\UPDATED SYSTEM\resources\views\components\hospitality\bento-dashboard.blade.php)

Recommended booking page composition:

1. `x-hospitality.global-calamity-banner`
2. `x-hospitality.stay-sail-hero`
3. Search and range picker section
4. Search results bento grid
5. Live total sidebar on desktop and sticky booking bar on mobile

React analog:

```tsx
<CalamityBanner status="red" boatBookingDisabled />
<StaySailHero activeMode="stay" />
<BookingShell>
  <UnifiedCalendar />
  <SearchResultsBento>
    <InventoryCard type="stay" />
    <InventoryCard type="sail" disabledByWeather />
    <WeatherStatusCard />
    <RevenueStatsCard />
    <QuickActionsCard />
  </SearchResultsBento>
  <LiveTotalSidebar />
  <StickyMobileReserveBar />
</BookingShell>
```

State contracts to keep shared across Blade or React:

- `weatherStatus`: `green | amber | red`
- `boatBookingDisabled`: derived from `weatherStatus === 'red'`
- `activeEngine`: `stay | sail`
- `bookingSummary`: subtotal, taxes, fuel, deposit, grand total
- `crossSell`: boat option matched to selected room dates

## Responsive Layout Strategy

Desktop:

- Use a 12-column asymmetric bento grid.
- Keep the unified calendar at `8 cols`, weather/status at `4 cols`.
- Keep the live total sidebar visible alongside results.
- Show hover-to-expand drawers inside cards instead of opening nested modals.

Tablet:

- Collapse to a 2-column bento rhythm.
- Move revenue stats and quick actions below primary search/calendar content.
- Keep the calamity header fixed at the top and full width.

Mobile:

- Convert every grid/table block into stacked information cards.
- Replace desktop sidebar with the sticky bottom reserve bar.
- Keep live total, taxes, and fuel in the sticky bar so cost is never hidden.
- Use slide-over drawers for details and policies.
- Keep search results reachable in two taps from any funnel step.

## UX Audit Checklist

- Calamity banner is above the logo and navigation on all breakpoints.
- A `red` status disables boat selection, booking CTA, and sail quantity steppers.
- Total cost is visible without scrolling on mobile and desktop.
- Fuel, tax, and deposit rules are included in the live total.
- Quantity inputs use steppers, not native dropdowns.
- Secondary details open in slide-over drawers, not modal-in-modal flows.
- Search results remain within two clicks from every funnel screen.
- Empty states suggest alternative dates or vessel classes immediately.
- Room and boat search results become stacked cards on mobile.
- Cross-sell only appears when the guest's selected dates overlap the sailing option.
- Weather widget uses warm-earth styling and never competes with the main CTA.
- The reserve CTA remains present even while drawers or filters are open.
- Hover expansion does not hide key pricing, availability, or safety information.
- Stay and sail selections are visually distinct inside the unified calendar.
