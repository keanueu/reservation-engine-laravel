@extends('admin.layouts.app')
@section('content')


 <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6">
        <div class="mb-8">
            <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-gray-50 tracking-tight">
                 Community Preparedness Hub
            </h1>
            <p class="text-sm md:text-base text-gray-500 dark:text-gray-400 mt-1">
                Essential information and preparedness measures for guests and residents.
            </p>
        </div>
                {{-- Admin alert form (only visible to admin users) --}}
             
<!-- Applied background gradient, flex layout, min-height, and padding directly to body -->
    <!-- Weather Container (Main Glass Card) -->
    <!-- Increased max-w for a landscape feel on desktop -->
    <div id="weather-app" class="w-full max-w-6xl xl:max-w-6xl mx-auto p-3 sm:p-4 lg:p-6 bg-white dark:bg-black/30 border-0 backdrop-blur-md mt-8  shadow-sm">

    
        <!-- Main Layout: Current Day (Left) and Forecast (Right) -->
        <!-- Adjusted grid for better landscape flow on large screens -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5">

            <!-- LEFT COLUMN (Span 2/3 on desktop) -->
            <div id="left-panel" class="lg:col-span-2 flex flex-col gap-4">

                <!-- ROW 1: Header, Main Temp, Details, and Sunrise/Sunset -->
                <div class="flex flex-col gap-4">

                    <!-- Header and Date -->
                    <div class="flex justify-between items-start">
                        <!-- Location -->
                        <div
                            class="forecast-item p-1 flex items-center transition flex-1 min-w-0">
                            <div class="flex items-center text-gray-900 dark:text-white flex-1 min-w-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-gray-700 dark:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                <span id="current-city" class="truncate max-w-xs"></span>
                                <span class="ml-1 text-gray-600 dark:text-white truncate max-w-[6rem]" id="current-country"></span>

                                <!-- City search input -->
                                 <div class="ml-3 flex items-center">
                                    <label id="city-input" placeholder="City"
                                        >
                                    <button id="city-search"
                                        ></button>
                                </div>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="text-right flex-shrink-0 ml-4 whitespace-nowrap">
                            <p class="text-sm  text-gray-700 dark:text-white" id="current-date">Wednesday, Aug 27</p>
                            <p class="text-xs text-gray-700 dark:text-white" id="current-time">11:32 AM</p>
                        </div>
                    </div>

                    <!-- Main Content Block (Temp, Icon, Details, Sunrise/Sunset) -->
                    <!-- Used a sub-grid here to arrange elements horizontally -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">

                        <!-- Col 1: Main Temp Block -->
                        <div
                            class="xl:col-span-1 flex flex-row items-center justify-between xl:flex-col xl:items-start p-3 sm:p-4 bg-white/3 dark:bg-black/40 border border-white/8 dark:border-black/40 shadow-md ">
                            <!-- Temperature -->
                            <div class="text-center xl:text-left">
                                <p class="text-3xl sm:text-4xl text-gray-900 dark:text-white  relative">
                                    <span id="current-temp">17</span>
                                    <span
                                    class="absolute top-0 right-[-1.5rem] xl:static xl:ml-1 xl:text-2xl font-normal">°C</span>
                                </p>
                                <p class="text-sm sm:text-base  text-gray-600 dark:text-white/80 mt-1" id="current-description">Overcast
                                    Clouds</p>
                                <p class="text-xs text-gray-500 dark:text-white/60 mt-1">H: <span id="high-temp">17°</span> L: <span
                                    id="low-temp">17°</span></p>
                            </div>

                            <!-- Weather Icon -->
                            <div class="flex-shrink-0">
                                <!-- Placeholder SVG for Sun -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-orange-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="4" fill="currentColor" />
                                    <path
                                        d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                                </svg>
                            </div>
                        </div>

                        <!-- Col 2 & 3: Current Details Grid (Spans 2 columns on XL screens) -->
                            <div id="details-grid-container"
                            class="md:col-span-2 xl:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-2">
                            <!-- Detail Card Component - Adjusted padding to p-3 -->
                            <div id="visibility"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-black/40 border border-white/8 dark:border-black/40 transition hover:bg-white/10 dark:hover:bg-gray-900/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M15 12c0 1.66-1.34 3-3 3s-3-1.34-3-3 1.34-3 3-3 3 1.34 3 3z" />
                                        <path
                                            d="M12 5.5s-6 7.5-12 7.5c6 0 12 7.5 12 7.5S18 13 24 13c-6 0-12-7.5-12-7.5z" />
                                    </svg>
                                    Visibility
                                </div>
                                <p class="text-base sm:text-lg text-gray-900 dark:text-white font-semibold" id="visibility-value">10.0 <span
                                    class="text-gray-600 dark:text-white ">km</span></p>
                            </div>

                            <div id="wind-speed"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-black/40 border border-white/8 dark:border-black/40 transition hover:bg-white/10 dark:hover:bg-gray-900/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M17.65 14.83a8 8 0 1 0-13.44 0" />
                                        <path d="M6 14h.01M18 14h.01" />
                                        <path d="M12 18v2" />
                                    </svg>
                                    Wind speed
                                </div>
                                <p class="text-base sm:text-lg text-gray-900 dark:text-white font-semibold" id="wind-speed-value">3.3 <span
                                    class="text-gray-600 dark:text-white ">m/s</span></p>
                            </div>

                            <div id="humidity"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-black/40 border border-white/8 dark:border-black/40 transition hover:bg-white/10 dark:hover:bg-gray-900/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 22s-6-5-6-10a6 6 0 0 1 12 0c0 5-6 10-6 10z" />
                                        <path d="M12 10V6" />
                                    </svg>
                                    Humidity
                                </div>
                                <p class="text-base sm:text-lg text-gray-900 dark:text-white font-semibold" id="humidity-value">61<span
                                    class="text-gray-600 dark:text-white ">%</span></p>
                            </div>

                            <div id="pressure"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-black/40 border border-white/8 dark:border-black/40 transition hover:bg-white/10 dark:hover:bg-gray-900/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M3 12a9 9 0 0 1 18 0" />
                                        <path d="M12 3a9 9 0 0 0 0 18" />
                                    </svg>
                                    Pressure
                                </div>
                                <p class="text-base sm:text-lg text-gray-900 dark:text-white font-semibold" id="pressure-value">1017 <span
                                    class="text-gray-600 dark:text-white ">hPa</span></p>
                            </div>

                            <div id="feels-like"
                                class="p-2 sm:p-3 bg-white/3 dark:bg-black/40 border border-white/8 dark:border-black/40 transition hover:bg-white/10 dark:hover:bg-gray-900/20 ">
                                <div class="flex items-center text-xs  text-gray-600 dark:text-white/80 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="5" />
                                        <path
                                            d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                                    </svg>
                                    Feels like
                                </div>
                                <p class="text-base sm:text-lg text-gray-900 dark:text-white font-semibold" id="feels-like-value">17<span
                                    class="text-gray-600 dark:text-white ">°C</span></p>
                            </div>
                        </div>

                        <!-- Col 4: Sunrise/Sunset (Spans 1 column on XL screens) -->
                        <div class="xl:col-span-1 grid grid-cols-2 xl:grid-cols-1 gap-2">
                            <!-- Sunrise -->
                            <div id="sunrise-card"
                                class="p-2 sm:p-3 text-gray-900 dark:text-white bg-orange-300 dark:bg-[#964B00] border border-orange-300 dark:border-orange-400 shadow transition hover:bg-orange-400 dark:hover:bg-[#7a3c00] ">
                                <div class="flex items-center text-sm  mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M12 5.5s-6 7.5-12 7.5c6 0 12 7.5 12 7.5S18 13 24 13c-6 0-12-7.5-12-7.5z" />
                                        <path d="M12 16v-1" />
                                        <path d="M18 13h1" />
                                        <path d="M5 13H4" />
                                        <path d="M18 10h1M5 10H4M16.5 8.5l.7.7M7.5 8.5l-.7-.7M12 6.5V5" />
                                    </svg>
                                    Sunrise
                                </div>
                                <p class="text-lg font-medium" id="sunrise-time">03:18 <span
                                        class="text-xs font-medium">PM</span></p>
                            </div>
                            <!-- Sunset -->
                            <div id="sunset-card"
                                  class="p-2 sm:p-3 text-gray-900 dark:text-white bg-gray-100 dark:bg-black border border-gray-200 dark:border-black shadow transition hover:bg-gray-200 dark:hover:bg-black ">
                                <div class="flex items-center text-sm  mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 18s-6-5-6-10c0-5 6-10 6-10s6 5 6 10c0 5-6 10-6 10z" />
                                        <path d="M12 16v-1" />
                                        <path d="M18 13h1" />
                                        <path d="M5 13H4" />
                                        <path d="M18 10h1M5 10H4M16.5 8.5l.7.7M7.5 8.5l-.7-.7M12 6.5V5" />
                                    </svg>
                                    Sunset
                                </div>
                                <p class="text-lg font-medium" id="sunset-time">04:36 <span
                                    class="text-xs font-medium">AM</span></p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">

                    <div id="monthly-forecast-panel"
                        class="p-2 sm:p-3 bg-white/5 dark:bg-black/40 border border-white/8 dark:border-black/40 shadow ">
                        <h3 class="text-gray-900 dark:text-white font-medium mb-3 flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            Monthly forecast - November 2025
                        </h3>

                        <div id="monthly-forecast-list" class="space-y-2">

                            <div
                                class="monthly-item p-2 sm:p-3 flex text-gray-900 dark:text-white justify-between items-center bg-white/50 dark:bg-black/30 hover:bg-gray-100 dark:hover:bg-gray-900/20 transition ">
                                <div class="w-1/4 text-xs font-medium">Week 1 (Nov 1 - 7)</div>
                                <div class="flex items-center w-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 text-orange-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                        <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-white/70">Sunny & Warm</span>
                                </div>
                                <div class="w-1/4 text-right text-xs  text-gray-600 dark:text-white/60">
                                    Avg. High: 28° <span class="text-gray-500 dark:text-white/50 ml-1">Avg. Low: 20°</span>
                                </div>
                            </div>

                            <div
                                class="monthly-item p-1 flex text-gray-900 dark:text-white justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-900/20 transition">
                                <div class="w-1/4 text-xs font-medium">Week 2 (Nov 8 - 14)</div>
                                <div class="flex items-center w-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 text-sky-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                        <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-white/70">Scattered Showers</span>
                                </div>
                                <div class="w-1/4 text-right text-xs  text-gray-600 dark:text-white/60">
                                    Avg. High: 25° <span class="text-gray-500 dark:text-white/50 ml-1">Avg. Low: 18°</span>
                                </div>
                            </div>

                            <div
                                class="monthly-item p-1 flex text-gray-900 dark:text-white justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-900/20 transition">
                                <div class="w-1/4 text-xs font-medium">Week 3 (Nov 15 - 21)</div>
                                <div class="flex items-center w-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 text-gray-300"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 17a5 5 0 0 1 0-10 4.5 4.5 0 0 1 4.5 4.5 4.5 4.5 0 0 0 0 9Z" />
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-white/70">Cooler & Cloudy</span>
                                </div>
                                <div class="w-1/4 text-right text-xs  text-gray-600 dark:text-white/60">
                                    Avg. High: 22° <span class="text-gray-500 dark:text-white/50 ml-1">Avg. Low: 15°</span>
                                </div>
                            </div>

                            <div
                                class="monthly-item p-1 flex text-gray-900 dark:text-white justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-900/20 transition">
                                <div class="w-1/4 text-xs font-medium">Week 4 (Nov 22 - 30)</div>
                                <div class="flex items-center w-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3 text-orange-400"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="4" fill="currentColor" />
                                        <path
                                            d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                                    </svg>
                                    <span class="text-xs text-gray-600 dark:text-white/70">Clearing Up</span>
                                </div>
                                <div class="w-1/4 text-right text-xs  text-gray-600 dark:text-white/60">
                                    Avg. High: 24° <span class="text-gray-500 dark:text-white/50 ml-1">Avg. Low: 16°</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <!-- RIGHT COLUMN: 5 Day Forecast (1/3 width on desktop) -->
            <div id="forecast-panel" class="lg:col-span-1 flex flex-col gap-2 p-2 sm:p-3 bg-white dark:bg-black/30 border border-gray-100 dark:border-black/40 shadow ">

                <h3 class="text-gray-900 dark:text-white font-medium mb-2 flex items-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                        <line x1="16" x2="16" y1="2" y2="6" />
                        <line x1="8" x2="8" y1="2" y2="6" />
                        <line x1="3" x2="21" y1="10" y2="10" />
                    </svg>
                    5 day forecast (summary)
                </h3>

                <!-- Forecast Item Loop -->
                    <div id="forecast-list" class="space-y-2">
                    <!-- Forecast Item Component -->
                    <div
                        class="forecast-item p-2 sm:p-3 flex justify-between items-center bg-white/50 dark:bg-black/25 hover:bg-gray-100 dark:hover:bg-gray-900/20 transition ">
                        <div class="flex items-center text-gray-900 dark:text-white">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 mr-3 text-sky-400"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                            </svg>
                            <div
                                class="forecast-item p-1 flex justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-900/20 transition">
                                <div class="flex items-center text-gray-900 dark:text-white">
                            </div>
                        </div>
                        <p class="text-sm sm:text-base  text-gray-900 dark:text-white">
                            17° <span class="text-gray-500 dark:text-white/50 ml-1">17°</span>
                        </p>
                    </div>

                    <div
                        class="forecast-item p-1 flex justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-900/20 transition">
                        <div class="flex items-center text-gray-900 dark:text-white">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-3 text-sky-400"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                            </svg>
                            <div>
                                <p class="text-xs font-medium text-gray-900 dark:text-white">Thu, Aug 28</p>
                                <p class="text-xs text-gray-600 dark:text-white/70">Broken Clouds</p>
                            </div>
                        </div>
                        <p class="text-xs  text-gray-900 dark:text-white">
                            25° <span class="text-gray-500 dark:text-white/50 ml-1">25°</span>
                        </p>
                    </div>

                    <div
                        class="forecast-item p-1 flex justify-between items-center bg-white/5 hover:bg-white/10 transition">
                        <div class="flex items-center">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-3 text-gray-300"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 17a5 5 0 0 1 0-10 4.5 4.5 0 0 1 4.5 4.5 4.5 4.5 0 0 0 0 9Z" />
                            </svg>
                            <div>
                                <p class="text-xs font-medium text-gray-900 dark:text-white">Fri, Aug 29</p>
                                <p class="text-xs text-gray-600 dark:text-white/70">Overcast Clouds</p>
                            </div>
                        </div>
                        <p class="text-xs  text-gray-900 dark:text-white">
                            24° <span class="text-gray-500 dark:text-white/50 ml-1">24°</span>
                        </p>
                    </div>

                    <div
                        class="forecast-item p-1 flex justify-between items-center bg-white/5 hover:bg-white/10 transition">
                        <div class="flex items-center">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-3 text-sky-400"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 14.89a8 8 0 0 1 12-3.78" />
                                <path d="M10.45 15a4 4 0 0 0-4-4H4" />
                            </svg>
                            <div>
                                <p class="text-xs font-medium text-gray-900 dark:text-white">Sat, Aug 30</p>
                                <p class="text-xs text-gray-600 dark:text-white/70">Broken Clouds</p>
                            </div>
                        </div>
                        <p class="text-xs  text-gray-900 dark:text-white">
                            24° <span class="text-gray-500 dark:text-white/50 ml-1">24°</span>
                        </p>
                    </div>

                    <div
                        class="forecast-item p-1 flex justify-between items-center bg-white/50 hover:bg-gray-100 dark:hover:bg-gray-900/20 transition">
                        <div class="flex items-center text-gray-900 dark:text-white">
                            <!-- Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-3 text-orange-400"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="4" fill="currentColor" />
                                <path
                                    d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                            </svg>
                            <div>
                                <p class="text-xs font-medium text-gray-900 dark:text-white">Sun, Aug 31</p>
                                <p class="text-xs text-gray-600 dark:text-white/70">Clear Sky</p>
                            </div>
                        </div>
                        <p class="text-xs  text-gray-900 dark:text-white">
                            24° <span class="text-gray-500 dark:text-white/50 ml-1">24°</span>
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>

                    @can('manage', App\Models\Alert::class)
                        @include('admin.partials.alert-form')
                    @else
                        @if(auth()->check() && optional(auth()->user())->usertype === 'admin')
                            {{-- Fallback: if no policy is defined, still allow users with usertype 'admin' to see the form --}}
                            @include('admin.partials.alert-form')
                        @endif
                    @endcan
    </div>

    


@endsection

@push('admin-scripts')
    <script src="/js/admin-weather.js"></script>
@endpush
