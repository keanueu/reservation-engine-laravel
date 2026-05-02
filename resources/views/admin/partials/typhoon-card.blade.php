<div id="typhoon-card" class="bg-white dark:bg-black border border-gray-200 dark:border-black p-4  shadow-sm">
  <div class="flex items-start justify-between gap-4">
    <div class="flex items-start gap-3 flex-1 min-w-0">
      <div id="typhoon-icon" class="w-10 h-10  flex-shrink-0 flex items-center justify-center text-white bg-green-500">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16z"/></svg>
      </div>
      <div class="min-w-0">
        <h2 class="font-semibold text-base text-gray-900 dark:text-white truncate"> Typhoon Status <span class="text-sm text-gray-500 dark:text-gray-400">(Pangasinan)</span></h2>
        <div id="typhoon-sub" class="mt-1 text-xs text-gray-600 dark:text-gray-400 truncate">Loading latest advisory…</div>
      </div>
          <div class="flex items-center gap-2">
            <span id="typhoon-status-pill" class="hidden ml-2 inline-flex items-center px-2 py-0.5  text-xs font-medium"></span>
          </div>
    </div>

    <div class="flex items-center gap-2">
      <button id="typhoon-refresh" class="px-3 py-1 text-sm bg-white border rounded text-gray-700 hover:bg-gray-50">Refresh</button>
    </div>
  </div>

  <div id="typhoon-body" class="mt-3 text-sm text-gray-700 dark:text-gray-200">
    <div class="typhoon-loading flex items-center gap-2 text-gray-600">
      <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
      </svg>
      Fetching latest advisory…
    </div>
  </div>

  <div id="typhoon-footer" class="mt-3 text-xs text-gray-500 dark:text-gray-400 flex items-center justify-between">
    <div id="typhoon-times" class="truncate">—</div>
    <a id="typhoon-details-link" href="#" class="text-blue-600 hover:underline hidden">View details</a>
  </div>
</div>

<!-- OpenWeather embed: map + small status note -->
<div id="openweather-embed" class="mt-4 bg-white dark:bg-black border border-gray-100 dark:border-black p-3 ">
  <h3 class="text-sm font-medium text-gray-800 dark:text-white">Local Weather (OpenWeather)</h3>
  <div class="mt-2">
    <iframe id="owm-iframe" src="https://openweathermap.org/weathermap?basemap=map&cities=true&layer=temperature&lat=15.95&lon=119.97&zoom=8" style="width:100%;height:260px;border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
  <div id="openweather-note" class="mt-2 text-xs text-gray-600 dark:text-gray-400">Status: Loading…</div>
</div>
