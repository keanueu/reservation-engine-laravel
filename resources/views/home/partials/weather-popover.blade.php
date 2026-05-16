{{-- Premium Weather Navbar Popover - Luxury Resort Style --}}
<div x-data="weatherPopover()" class="relative">
    {{-- Minimalist Trigger Button --}}
    <button @click="toggle()" 
            class="flex items-center gap-2 px-3 py-1.5 text-[14px] font-medium text-black hover:text-[#63360D] transition-all duration-300 font-['Raleway'] group"
            :class="{ 'text-[#63360D]': isOpen }">
        {{-- Weather Icon --}}
        <span id="navbar-weather-icon" class="material-symbols-outlined text-base group-hover:scale-110 transition-transform duration-300">wb_sunny</span>
        {{-- Current Temperature --}}
        <span id="navbar-temp" class="font-['Raleway'] text-md font-semibold">--°</span>
    </button>

    {{-- Backdrop (Mobile only) --}}
    <div x-show="isOpen" x-cloak class="md:hidden fixed inset-0 bg-black/20 z-[90]" @click="close()"></div>

    {{-- Popover Dropdown --}}
    <div x-show="isOpen" 
         x-cloak
         @click.outside="close()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed md:absolute inset-x-4 md:inset-x-auto md:left-0 top-[60px] md:top-full mt-3 w-auto md:w-[580px] max-w-[calc(100vw-2rem)] bg-white border border-gray-100 shadow-2xl z-[100] overflow-hidden mx-auto md:mx-0">
        
        {{-- Header Section --}}
        <div class="px-6 py-4 border-b border-gray-50 bg-gradient-to-r from-[#63360D] to-[#8B4E14]">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-white/80 text-lg">location_on</span>
                    <span id="popover-city" class="text-sm font-semibold tracking-wide 
">Dasol</span>
                    <span id="popover-country" class="text-xs text-white/60">Pangasinan, PH</span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p id="popover-date" class="text-[11px] text-white/70 font-medium 
 tracking-tighter">Loading...</p>
                        <p id="popover-time" class="text-sm font-bold">--:--</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content - Two Column Layout --}}
        <div class="flex flex-col md:flex-row">
            {{-- Left Column: Current Conditions --}}
            <div class="flex-1 px-7 py-6 border-r border-gray-50">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-[11px] font-bold text-[#63360D] 
 text-teal-800 font-bold">Current Climate</h3>
                    <div class="flex gap-1.5">
                        <input id="popover-city-input" 
                               placeholder="Other city..." 
                               class="w-28 px-2 py-1 text-[11px] border border-gray-200 focus:border-[#63360D] outline-none transition-colors">
                        <button id="popover-city-search" 
                                class="p-1 bg-[#63360D] text-white hover:bg-black transition-colors">
                            <span class="material-symbols-outlined text-sm">search</span>
                        </button>
                    </div>
                </div>
            
                {{-- Main Weather Display --}}
                <div class="flex items-center gap-8 mb-8">
                    <div class="flex-1">
                        <div class="flex items-baseline gap-1 mb-1">
                            <span id="popover-temp" class="text-5xl font-bold text-black">--</span>
                            <span class="text-lg font-medium text-black/40">°C</span>
                        </div>
                        <p id="popover-description" class="text-sm text-[#63360D] font-medium capitalize mb-2 tracking-wide">Loading...</p>
                        <div class="flex gap-4 text-xs font-medium text-black/50">
                            <span>High: <span id="popover-high" class="text-black">--°</span></span>
                            <span>Low: <span id="popover-low" class="text-black">--°</span></span>
                        </div>
                    </div>
                    <div class="w-20 h-20 flex items-center justify-center bg-orange-50/50 rounded-full">
                        <span id="weather-main-icon" class="material-symbols-outlined text-[#63360D] text-5xl">wb_sunny</span>
                    </div>
                </div>

                {{-- Weather Details Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50/50 p-4 border border-gray-100 flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#63360D]/60 text-lg">thermostat</span>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold 
 tracking-tighter">Feels Like</p>
                            <p id="popover-feels-like" class="text-sm font-semibold text-black">--°C</p>
                        </div>
                    </div>
                    <div class="bg-gray-50/50 p-4 border border-gray-100 flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#63360D]/60 text-lg">humidity_low</span>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold 
 tracking-tighter">Humidity</p>
                            <p id="popover-humidity" class="text-sm font-semibold text-black">--%</p>
                        </div>
                    </div>
                    <div class="bg-gray-50/50 p-4 border border-gray-100 flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#63360D]/60 text-lg">air</span>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold 
 tracking-tighter">Wind Speed</p>
                            <p id="popover-wind" class="text-sm font-semibold text-black">-- m/s</p>
                        </div>
                    </div>
                    <div class="bg-gray-50/50 p-4 border border-gray-100 flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#63360D]/60 text-lg">visibility</span>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold 
 tracking-tighter">Visibility</p>
                            <p id="popover-visibility" class="text-sm font-semibold text-black">-- km</p>
                        </div>
                    </div>
                </div>

                {{-- Sun Times --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-orange-50/30 p-3 border border-orange-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-500 text-lg">wb_sunny</span>
                            <span class="text-[10px] font-bold text-gray-500 
 tracking-tighter">Sunrise</span>
                        </div>
                        <p id="popover-sunrise" class="text-xs font-bold text-black">--:--</p>
                    </div>
                    <div class="bg-indigo-50/30 p-3 border border-indigo-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-500 text-lg">bedtime</span>
                            <span class="text-[10px] font-bold text-gray-500 
 tracking-tighter">Sunset</span>
                        </div>
                        <p id="popover-sunset" class="text-xs font-bold text-black">--:--</p>
                    </div>
                </div>
            </div>

            {{-- Right Column: 5-Day Forecast --}}
            <div class="w-full md:w-60 bg-gray-50/30 px-6 py-6">
                <h3 class="text-[11px] font-bold text-[#63360D] 
 text-teal-800 font-bold mb-6">Upcoming Days</h3>
                <div id="popover-forecast" class="space-y-4">
                    @for($i = 0; $i < 5; $i++)
                        <div class="flex items-center justify-between group cursor-default">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-gray-300 group-hover:text-[#63360D] transition-colors text-base">cloud</span>
                                <div>
                                    <p class="text-[11px] font-bold text-black 
 tracking-tighter">Loading...</p>
                                    <p class="text-[10px] text-gray-400 font-medium capitalize">--</p>
                                </div>
                            </div>
                            <p class="text-xs font-bold text-black">--°</p>
                        </div>
                    @endfor
                </div>

                <div class="mt-10 pt-6 border-t border-gray-100">
                    <p class="text-[9px] text-gray-400 font-bold 
 leading-relaxed">
                        Data provided by<br>OpenWeather API
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function weatherPopover() {
    return {
        isOpen: false,
        
        toggle() {
            this.isOpen = !this.isOpen;
        },
        
        close() {
            this.isOpen = false;
        },
        
        init() {
            // Check Material Symbols availability
            this.validateMaterialSymbols();
            
            // Initialize weather data loading
            this.loadWeatherData();
            
            // Set up city search
            const searchBtn = document.getElementById('popover-city-search');
            const searchInput = document.getElementById('popover-city-input');
            
            if (searchBtn && searchInput) {
                searchBtn.addEventListener('click', () => {
                    const city = searchInput.value.trim();
                    if (city) {
                        this.loadWeatherData(city);
                    }
                });
                
                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        const city = e.target.value.trim();
                        if (city) {
                            this.loadWeatherData(city);
                        }
                    }
                });
            }
            
            // Update clock every second
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
        },
        
        validateMaterialSymbols() {
            // Check if Material Symbols is loaded
            const testIcon = document.createElement('span');
            testIcon.className = 'material-symbols-outlined';
            testIcon.textContent = 'wb_sunny';
            testIcon.style.position = 'absolute';
            testIcon.style.left = '-9999px';
            document.body.appendChild(testIcon);
            
            const computed = window.getComputedStyle(testIcon);
            const fontFamily = computed.getPropertyValue('font-family');
            
            console.log('Material Symbols check:', {
                fontFamily,
                isLoaded: fontFamily.includes('Material Symbols')
            });
            
            document.body.removeChild(testIcon);
            
            // If Material Symbols isn't loaded, add fallback CSS
            if (!fontFamily.includes('Material Symbols')) {
                console.warn('Material Symbols not detected, adding fallback styles');
                this.addFallbackStyles();
            }
        },
        
        addFallbackStyles() {
            const style = document.createElement('style');
            style.textContent = `
                .material-symbols-outlined {
                    font-family: 'Material Symbols Outlined';
                    font-weight: normal;
                    font-style: normal;
                    font-size: 24px;
                    line-height: 1;
                    letter-spacing: normal;
                    text-transform: none;
                    display: inline-block;
                    white-space: nowrap;
                    word-wrap: normal;
                    direction: ltr;
                    -webkit-font-feature-settings: 'liga';
                    -webkit-font-smoothing: antialiased;
                }
            `;
            document.head.appendChild(style);
        },
        
        updateClock() {
            const dateEl = document.getElementById('popover-date');
            const timeEl = document.getElementById('popover-time');
            
            if (dateEl && timeEl) {
                const now = new Date();
                dateEl.textContent = now.toLocaleDateString('en-US', { 
                    weekday: 'short', 
                    month: 'short', 
                    day: 'numeric' 
                });
                timeEl.textContent = now.toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    hour12: true 
                });
            }
        },
        
        async loadWeatherData(city = 'Dasol') {
            try {
                // Load current weather
                const currentRes = await fetch(`/weather/current?city=${encodeURIComponent(city)}`);
                if (currentRes.ok) {
                    const currentData = await currentRes.json();
                    this.updateCurrentWeather(currentData);
                }
                
                // Load forecast
                const forecastRes = await fetch(`/weather/forecast?city=${encodeURIComponent(city)}`);
                if (forecastRes.ok) {
                    const forecastData = await forecastRes.json();
                    this.updateForecast(forecastData.list);
                }
                
            } catch (error) {
                console.error('Weather data loading failed:', error);
            }
        },
        
        updateCurrentWeather(data) {
            console.log('Updating current weather:', data);
            
            // Update navbar trigger
            const navbarTemp = document.getElementById('navbar-temp');
            if (navbarTemp && data.main) {
                navbarTemp.textContent = `${Math.round(data.main.temp)}°`;
            }
            
            // Update navbar weather icon with validation
            const navbarIcon = document.getElementById('navbar-weather-icon');
            if (navbarIcon && data.weather?.[0]) {
                const iconName = this.getWeatherIconName(data.weather[0].main, data.weather[0].icon);
                console.log('Setting navbar icon name:', iconName);
                
                navbarIcon.textContent = iconName;
                navbarIcon.className = 'material-symbols-outlined text-inherit text-base group-hover:scale-110 transition-transform duration-300';
            }
            
            // Update main weather icon with validation
            const mainIcon = document.getElementById('weather-main-icon');
            if (mainIcon && data.weather?.[0]) {
                const iconName = this.getWeatherIconName(data.weather[0].main, data.weather[0].icon);
                console.log('Setting main icon name:', iconName);
                
                mainIcon.textContent = iconName;
                mainIcon.className = 'material-symbols-outlined text-[#63360D] text-5xl';
            }
            
            // Update popover content
            const elements = {
                'popover-city': data.name,
                'popover-country': data.sys?.country || '',
                'popover-temp': data.main ? Math.round(data.main.temp) : '--',
                'popover-description': data.weather?.[0]?.description || 'Loading...',
                'popover-high': data.main ? `${Math.round(data.main.temp_max)}°` : '--°',
                'popover-low': data.main ? `${Math.round(data.main.temp_min)}°` : '--°',
                'popover-feels-like': data.main ? `${Math.round(data.main.feels_like)}°C` : '--°C',
                'popover-humidity': data.main ? `${data.main.humidity}%` : '--%',
                'popover-wind': data.wind ? `${data.wind.speed} m/s` : '-- m/s',
                'popover-visibility': data.visibility ? `${(data.visibility / 1000).toFixed(1)} km` : '-- km',
                'popover-sunrise': data.sys?.sunrise ? this.formatTime(data.sys.sunrise) : '--:--',
                'popover-sunset': data.sys?.sunset ? this.formatTime(data.sys.sunset) : '--:--'
            };
            
            Object.entries(elements).forEach(([id, value]) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            });
        },
        
        updateForecast(forecastList) {
            console.log('Updating forecast:', forecastList);
            
            const container = document.getElementById('popover-forecast');
            if (!container || !forecastList) {
                console.error('Forecast container or data missing');
                return;
            }
            
            container.innerHTML = '';
            
            // Group by day
            const dailyData = {};
            forecastList.forEach(item => {
                const date = new Date(item.dt_txt).toLocaleDateString('en-US', { 
                    weekday: 'short', 
                    month: 'short', 
                    day: 'numeric' 
                });
                if (!dailyData[date]) dailyData[date] = [];
                dailyData[date].push(item);
            });
            
            console.log('Grouped forecast data:', dailyData);
            
            // Display first 5 days
            Object.entries(dailyData).slice(0, 5).forEach(([day, entries], index) => {
                const temps = entries.map(e => e.main.temp);
                const maxTemp = Math.round(Math.max(...temps));
                const minTemp = Math.round(Math.min(...temps));
                const weather = entries[Math.floor(entries.length / 2)].weather[0];
                
                console.log(`Day ${index + 1} (${day}):`, { weather, maxTemp, minTemp });
                
                // Get weather icon name with validation
                const iconName = this.getWeatherIconName(weather.main, weather.icon);
                console.log(`Day ${index + 1} icon name:`, iconName);
                
                // Validate icon name
                const validIconName = iconName || 'cloud';
                
                const itemHTML = `
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0 group cursor-default">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gray-300 group-hover:text-[#63360D] transition-colors text-base" data-weather="${weather.main}" data-icon="${weather.icon}">${validIconName}</span>
                            <div>
                                <p class="text-[11px] font-bold text-black 
 tracking-tighter">${day}</p>
                                <p class="text-[10px] text-gray-400 font-medium capitalize">${weather.description}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-black">${maxTemp}° / ${minTemp}°</p>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHTML);
            });
            
            // No need for icon refresh with Material Symbols
            console.log('Forecast updated with Material Symbols icons');
        },
        
        formatTime(timestamp) {
            const date = new Date(timestamp * 1000);
            return date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit', 
                hour12: true 
            });
        },
        
        getWeatherIconName(weatherMain, weatherIcon) {
            // Material Symbols weather icon mapping
            const iconMap = {
                // Clear weather - always use sunny for clear sky
                'Clear': 'wb_sunny',
                
                // Cloudy weather
                'Clouds': 'cloud',
                'Overcast': 'cloud',
                
                // Rain weather
                'Rain': 'rainy',
                'Drizzle': 'rainy',
                'Shower': 'rainy',
                
                // Storm weather
                'Thunderstorm': 'thunderstorm',
                'Storm': 'thunderstorm',
                
                // Snow weather
                'Snow': 'ac_unit',
                'Sleet': 'ac_unit',
                'Blizzard': 'ac_unit',
                
                // Wind weather
                'Squall': 'air',
                'Tornado': 'air',
                'Windy': 'air',
                
                // Atmospheric conditions
                'Mist': 'foggy',
                'Smoke': 'foggy', 
                'Haze': 'foggy',
                'Dust': 'foggy',
                'Fog': 'foggy',
                'Sand': 'foggy',
                'Ash': 'foggy'
            };
            
            // Debug logging
            console.log('Weather mapping input:', { weatherMain, weatherIcon });
            
            // Check if it's night time
            const isNight = weatherIcon && weatherIcon.endsWith('n');
            console.log('Is night time:', isNight);
            
            // Handle clear weather - ALWAYS use sunny icon (no moon for clear sky)
            if (weatherMain === 'Clear') {
                console.log('Clear weather final icon: wb_sunny (always sunny, never moon)');
                return 'wb_sunny';
            }
            
            // Handle cloudy weather - always use cloud for consistency
            if (weatherMain === 'Clouds') {
                console.log('Cloudy weather final icon: cloud');
                return 'cloud';
            }
            
            // Try exact match first
            if (iconMap[weatherMain]) {
                console.log('Exact match final icon:', iconMap[weatherMain]);
                return iconMap[weatherMain];
            }
            
            // Fallback with partial matching
            const weatherLower = weatherMain ? weatherMain.toLowerCase() : '';
            
            if (weatherLower.includes('rain') || weatherLower.includes('drizzle')) {
                console.log('Rain fallback final icon: rainy');
                return 'rainy';
            }
            
            if (weatherLower.includes('cloud')) {
                console.log('Cloud fallback final icon: cloud');
                return 'cloud';
            }
            
            if (weatherLower.includes('sun') || weatherLower.includes('clear')) {
                console.log('Sun fallback final icon: wb_sunny');
                return 'wb_sunny';
            }
            
            if (weatherLower.includes('snow') || weatherLower.includes('ice')) {
                console.log('Snow fallback final icon: ac_unit');
                return 'ac_unit';
            }
            
            if (weatherLower.includes('storm') || weatherLower.includes('thunder')) {
                console.log('Storm fallback final icon: thunderstorm');
                return 'thunderstorm';
            }
            
            if (weatherLower.includes('wind')) {
                console.log('Wind fallback final icon: air');
                return 'air';
            }
            
            // Ultimate fallback
            console.log('Ultimate fallback final icon: cloud');
            return 'cloud';
        }
    }
}
</script>
