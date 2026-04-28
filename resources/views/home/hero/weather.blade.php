<div id="weather-side-panel"
    class="w-full max-w-sm mx-auto p-4 bg-white shadow-lg font-[Manrope] h-full overflow-y-auto border border-gray-200">

    <div class="grid grid-cols-1 gap-4">

        <div id="header-section" class="flex flex-col gap-3">

            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center text-base font-medium text-gray-800 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-[#964B00]" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span id="current-city" class="font-semibold">City Name</span>
                        <span class="ml-1 text-gray-500 text-sm" id="current-country">Country</span>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-xs text-gray-500" id="current-date">Wed, Nov 27</p>
                    <p class="text-sm text-gray-800 font-medium" id="current-time">03:10 PM</p>
                </div>
            </div>

            <div class="flex items-center text-sm">
                <input id="city-input" placeholder="Search City..."
                    class="w-full p-2 bg-gray-100 text-gray-800 border border-gray-300 focus:ring-orange-500 focus:border-orange-500 text-sm shadow-inner" />
                <button id="city-search"
                    class="ml-2 p-2 bg-[#964B00] hover:bg-[#7a3c00] text-white text-sm transition shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>
                </button>
            </div>
        </div>

        <hr class="border-gray-100">

        <div id="current-conditions-card"
            class="flex flex-row items-center justify-between p-4 bg-orange-50 border border-orange-200 shadow-md transition hover:shadow-lg">
            <div class="text-left">
                <p class="text-6xl text-gray-800 relative">
                    <span id="current-temp">17</span>
                    <span class="absolute top-1 right-[-1.5rem] ml-1 text-3xl font-normal text-[#964B00]">°C</span>
                </p>
                <p class="text-sm text-gray-600 mt-2" id="current-description">Overcast Clouds</p>
                <p class="text-xs text-gray-500 mt-1">H: <span class="text-gray-800 font-medium"
                        id="high-temp">17°</span> L: <span class="text-gray-600" id="low-temp">17°</span></p>
            </div>

            <div class="flex-shrink-0">
                <div class="sun-container">
                    <svg id="spinning-sun" xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-[#964B00]"
                        viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5" fill="currentColor" />
                        <path stroke="currentColor" stroke-width="2"
                            d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                    </svg>
                </div>
            </div>
        </div>

        <hr class="border-gray-100">

        <h3 class="text-gray-800 font-medium flex items-center text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-[#964B00]" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.5 10a2.5 2.5 0 0 0-5 0v1h5zM12 18h.01M16 16v1a5 5 0 0 1-10 0v-1" />
            </svg>
            Current Details
        </h3>
        <div id="details-grid-container" class="grid grid-cols-2 gap-3">

            <div id="visibility"
                class="p-3 bg-white border border-gray-200 transition hover:shadow-sm hover:border-orange-400">
                <p class="text-xs text-gray-500 mb-1">Visibility</p>
                <p class="text-lg text-gray-800 font-semibold" id="visibility-value">10.0 <span
                        class="text-gray-400 text-sm">km</span></p>
            </div>

            <div id="wind-speed"
                class="p-3 bg-white border border-gray-200 transition hover:shadow-sm hover:border-orange-400">
                <p class="text-xs text-gray-500 mb-1">Wind Speed</p>
                <p class="text-lg text-gray-800 font-semibold" id="wind-speed-value">3.3 <span
                        class="text-gray-400 text-sm">m/s</span></p>
            </div>

            <div id="humidity"
                class="p-3 bg-white border border-gray-200 transition hover:shadow-sm hover:border-orange-400">
                <p class="text-xs text-gray-500 mb-1">Humidity</p>
                <p class="text-lg text-gray-800 font-semibold" id="humidity-value">61<span
                        class="text-gray-400 text-sm">%</span></p>
            </div>

            <div id="pressure"
                class="p-3 bg-white border border-gray-200 transition hover:shadow-sm hover:border-orange-400">
                <p class="text-xs text-gray-500 mb-1">Pressure</p>
                <p class="text-lg text-gray-800 font-semibold" id="pressure-value">1017 <span
                        class="text-gray-400 text-sm">hPa</span></p>
            </div>

            <div id="feels-like"
                class="p-3 bg-white border border-gray-200 transition hover:shadow-sm hover:border-orange-400 col-span-2">
                <p class="text-xs text-gray-500 mb-1">Feels Like</p>
                <p class="text-lg text-gray-800 font-semibold" id="feels-like-value">17<span
                        class="text-gray-400 text-sm">°C</span></p>
            </div>
        </div>

        <hr class="border-gray-100">

        <h3 class="text-gray-800 font-medium flex items-center text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-[#964B00]" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M12 2v1M4.22 4.22l.71.71M2 12h1M4.22 19.78l.71-.71M12 22v-1M19.78 19.78l-.71-.71M22 12h-1M19.78 4.22l-.71.71M12 18a6 6 0 0 0 0-12v6" />
            </svg>
            Solar Times
        </h3>
        <div class="grid grid-cols-2 gap-3">
            <div id="sunrise-card"
                class="p-3 bg-white border border-gray-200 transition hover:shadow-sm hover:border-orange-400">
                <p class="text-xs text-gray-500 mb-1">Sunrise</p>
                <p class="text-lg font-medium text-gray-800" id="sunrise-time">03:18 <span
                        class="text-xs font-medium text-gray-500">PM</span></p>
            </div>
            <div id="sunset-card"
                class="p-3 bg-white border border-gray-200 transition hover:shadow-sm hover:border-orange-400">
                <p class="text-xs text-gray-500 mb-1">Sunset</p>
                <p class="text-lg font-medium text-gray-800" id="sunset-time">04:36 <span
                        class="text-xs font-medium text-gray-500">AM</span></p>
            </div>
        </div>

        <hr class="border-gray-100">

        <div id="forecast-panel" class="flex flex-col gap-3 p-3 bg-gray-50 border border-gray-200 shadow-md">

            <h3 class="text-gray-800 font-semibold flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-[#964B00]" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                    <line x1="16" x2="16" y1="2" y2="6" />
                    <line x1="8" x2="8" y1="2" y2="6" />
                    <line x1="3" x2="21" y1="10" y2="10" />
                </svg>
                5 Day Outlook
            </h3>

            <div id="forecast-list" class="space-y-2">
                @foreach([
                    ['day' => 'Today', 'desc' => 'Broken Clouds', 'high' => '17°', 'low' => '17°'],
                    ['day' => 'Thu, Aug 28', 'desc' => 'Broken Clouds', 'high' => '25°', 'low' => '25°'],
                    ['day' => 'Fri, Aug 29', 'desc' => 'Overcast Clouds', 'high' => '24°', 'low' => '24°'],
                    ['day' => 'Sat, Aug 30', 'desc' => 'Broken Clouds', 'high' => '24°', 'low' => '24°'],
                    ['day' => 'Sun, Aug 31', 'desc' => 'Clear Sky', 'high' => '24°', 'low' => '24°'],
                ] as $forecast)
                    <div class="forecast-item p-2 flex justify-between items-center bg-white hover:bg-orange-100 transition border border-gray-200">
                        <div>
                            <p class="text-xs font-medium text-gray-800">{{ $forecast['day'] }}</p>
                            <p class="text-xs text-gray-500">{{ $forecast['desc'] }}</p>
                        </div>
                        <p class="text-sm font-medium text-gray-800">
                            {{ $forecast['high'] }} <span class="text-gray-500 ml-1">{{ $forecast['low'] }}</span>
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        <hr class="border-gray-100">

        <div id="monthly-forecast-panel" class="p-3 bg-gray-50 border border-gray-200 shadow-md">
            <h3 class="text-gray-800 font-semibold mb-3 flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-[#964B00]" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                    <line x1="16" x2="16" y1="2" y2="6" />
                    <line x1="8" x2="8" y1="2" y2="6" />
                    <line x1="3" x2="21" y1="10" y2="10" />
                </svg>
                Monthly Forecast (Nov 2025)
            </h3>

            <div id="monthly-forecast-list" class="space-y-2">
                @foreach([
                    ['week' => 'Week 1', 'desc' => 'Sunny & Warm', 'range' => '28° / 20°'],
                    ['week' => 'Week 2', 'desc' => 'Scattered Showers', 'range' => '25° / 18°'],
                    ['week' => 'Week 3', 'desc' => 'Cooler & Cloudy', 'range' => '22° / 15°'],
                    ['week' => 'Week 4', 'desc' => 'Clear Intervals', 'range' => '24° / 17°'],
                ] as $month)
                    <div class="monthly-item p-2 flex justify-between items-center bg-white hover:bg-orange-100 transition border border-gray-200">
                        <div class="w-1/4 text-xs font-medium text-gray-800">{{ $month['week'] }}</div>
                        <div class="flex items-center w-1/2">
                            <span class="text-xs text-gray-500">{{ $month['desc'] }}</span>
                        </div>
                        <div class="w-1/4 text-right text-xs font-medium text-gray-800">
                            {{ $month['range'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
