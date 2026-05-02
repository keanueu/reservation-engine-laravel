{{-- Professional Weather Navbar Popover - Original Style --}}
<div x-data="weatherPopover()" class="relative">
    {{-- Minimalist Trigger Button --}}
    <button @click="toggle()" 
            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition-colors duration-200 border border-transparent hover:border-slate-200"
            :class="{ 'bg-slate-50 border-slate-200': isOpen }">
        {{-- Weather Icon --}}
        <span id="navbar-weather-icon" class="material-symbols-outlined text-slate-600 text-base">wb_sunny</span>
        {{-- Current Temperature --}}
        <span id="navbar-temp" class="font-sans font-semibold text-slate-900">--°</span>
    </button>

    {{-- Popover Dropdown --}}
    <div x-show="isOpen" 
         x-cloak
         @click.outside="close()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         class="absolute right-0 top-full mt-2 w-[600px] bg-white border border-slate-200 shadow-lg z-50 overflow-hidden">
        
        {{-- Header Section --}}
        <div class="px-6 py-3 border-b border-slate-100 bg-slate-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-500 text-base">location_on</span>
                    <span id="popover-city" class="text-sm font-semibold text-slate-900">Dasol</span>
                    <span id="popover-country" class="text-xs text-slate-500">PH</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p id="popover-date" class="text-xs text-slate-500">Loading...</p>
                        <p id="popover-time" class="text-xs font-medium text-slate-700">--:--</p>
                    </div>
                    {{-- City Search --}}
                    <div class="flex gap-2">
                        <input id="popover-city-input" 
                               placeholder="Search city..." 
                               class="w-32 px-2 py-1.5 text-xs border border-slate-200 focus:border-slate-400 focus:ring-1 focus:ring-slate-400 outline-none transition-colors">
                        <button id="popover-city-search" 
                                class="px-2 py-1.5 bg-slate-900 text-white hover:bg-slate-800 transition-colors">
                            <span class="material-symbols-outlined text-xs">search</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content - Two Column Layout --}}
        <div class="flex">
            {{-- Left Column: Current Conditions --}}
            <div class="flex-1 px-6 py-4 border-r border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">Current Conditions</h3>
            
            {{-- Main Weather Display --}}
            <div class="flex items-center gap-6 mb-4">
                <div class="flex-1">
                    <div class="flex items-baseline gap-2 mb-2">
                        <span id="popover-temp" class="text-4xl font-bold text-slate-900">--</span>
                        <span class="text-xl text-slate-600">°C</span>
                    </div>
                    <p id="popover-description" class="text-sm text-slate-500 capitalize mb-2">Loading...</p>
                    <div class="flex gap-4 text-xs text-slate-500">
                        <span>H: <span id="popover-high" class="font-medium text-slate-700">--°</span></span>
                        <span>L: <span id="popover-low" class="font-medium text-slate-700">--°</span></span>
                    </div>
                </div>
                <div class="w-16 h-16 flex items-center justify-center">
                    <span id="weather-main-icon" class="material-symbols-outlined text-slate-400 text-4xl">wb_sunny</span>
                </div>
            </div>

            {{-- Weather Details Grid --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-slate-50 p-3 text-center">
                    <p class="text-xs text-slate-500 mb-1">Feels Like</p>
                    <p id="popover-feels-like" class="text-sm font-semibold text-slate-900">--°C</p>
                </div>
                <div class="bg-slate-50 p-3 text-center">
                    <p class="text-xs text-slate-500 mb-1">Humidity</p>
                    <p id="popover-humidity" class="text-sm font-semibold text-slate-900">--%</p>
                </div>
                <div class="bg-slate-50 p-3 text-center">
                    <p class="text-xs text-slate-500 mb-1">Wind</p>
                    <p id="popover-wind" class="text-sm font-semibold text-slate-900">-- m/s</p>
                </div>
                <div class="bg-slate-50 p-3 text-center">
                    <p class="text-xs text-slate-500 mb-1">Visibility</p>
                    <p id="popover-visibility" class="text-sm font-semibold text-slate-900">-- km</p>
                </div>
            </div>

            {{-- Sun Times --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gradient-to-br from-orange-50 to-yellow-50 p-3 text-center border border-orange-100">
                    <div class="flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-orange-500 text-lg">wb_sunny</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-1">Sunrise</p>
                    <p id="popover-sunrise" class="text-sm font-semibold text-slate-900">--:--</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-3 text-center border border-purple-100">
                    <div class="flex items-center justify-center mb-2">
                        <span class="material-symbols-outlined text-purple-500 text-lg">bedtime</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-1">Sunset</p>
                    <p id="popover-sunset" class="text-sm font-semibold text-slate-900">--:--</p>
                </div>
            </div>
        </div>

            {{-- Right Column: 5-Day Forecast --}}
            <div class="w-64 px-4 py-4">
                <h3 class="text-sm font-semibold text-slate-900 mb-3">5-Day Forecast</h3>
                <div id="popover-forecast" class="space-y-2">
                    {{-- Forecast items will be populated by JavaScript --}}
                    @for($i = 0; $i < 5; $i++)
                        <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-400 text-sm">cloud</span>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-900">Loading...</p>
                                    <p class="text-xs text-slate-500">--</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-slate-900">--°</p>
                            </div>
                        </div>
                    @endfor
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
                navbarIcon.className = 'material-symbols-outlined text-zinc-400 text-base';
            }
            
            // Update main weather icon with validation
            const mainIcon = document.getElementById('weather-main-icon');
            if (mainIcon && data.weather?.[0]) {
                const iconName = this.getWeatherIconName(data.weather[0].main, data.weather[0].icon);
                console.log('Setting main icon name:', iconName);
                
                mainIcon.textContent = iconName;
                mainIcon.className = 'material-symbols-outlined text-zinc-400 text-4xl';
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
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-50 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-zinc-400 text-sm" data-weather="${weather.main}" data-icon="${weather.icon}">${validIconName}</span>
                            <div>
                                <p class="text-xs font-medium text-slate-900">${day}</p>
                                <p class="text-xs text-slate-500 capitalize">${weather.description}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-semibold text-slate-900">${maxTemp}° / ${minTemp}°</p>
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