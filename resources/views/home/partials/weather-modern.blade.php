{{-- Modern Google-Style Weather Component --}}
<div x-data="modernWeather()" class="relative">
    {{-- Trigger Button --}}
    <button @click="toggle()" 
            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-50 transition-colors duration-200 border border-transparent hover:border-slate-200"
            :class="{ 'bg-slate-50 border-slate-200': isOpen }">
        <span id="navbar-weather-icon" class="material-symbols-outlined text-slate-600 text-base">wb_sunny</span>
        <span id="navbar-temp" class="font-sans font-semibold text-slate-900">--°</span>
    </button>

    {{-- Modern Weather Popover --}}
    <div x-show="isOpen" 
         x-cloak
         @click.outside="close()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         class="absolute right-0 top-full mt-2 w-[750px] bg-[#202124] text-[#bdc1c6] rounded-xl font-sans border border-[#3c4043] shadow-2xl z-50 overflow-hidden">
        
        {{-- Header: Current Weather --}}
        <div class="flex justify-between items-start p-6 pb-4">
            <div class="flex items-center gap-4">
                <span id="main-weather-icon" class="material-symbols-outlined text-7xl text-yellow-500" style="font-variation-settings: 'FILL' 1">
                    sunny
                </span>
                <div class="flex items-baseline">
                    <span id="main-temp" class="text-white text-6xl font-light">--</span>
                    <span class="text-2xl ml-1 text-white">°C</span>
                    <div class="mx-2 h-6 w-[1px] bg-[#5f6368]"></div>
                    <span class="text-2xl text-[#9aa0a6] hover:text-white cursor-pointer">°F</span>
                </div>
                <div class="ml-4 text-sm space-y-0.5">
                    <p>Precipitation: <span id="precipitation">0</span>%</p>
                    <p>Humidity: <span id="humidity">--</span>%</p>
                    <p>Wind: <span id="wind">--</span> km/h</p>
                </div>
            </div>
            
            <div class="text-right">
                <h2 class="text-white text-2xl font-normal">Weather</h2>
                <p id="current-time" class="text-[#9aa0a6]">Loading...</p>
                <p id="weather-desc" class="text-[#9aa0a6] capitalize">Loading...</p>
            </div>
        </div>

        {{-- Tabs Section --}}
        <div class="flex gap-6 border-b border-[#3c4043] text-sm font-medium px-6">
            <div class="relative pb-2 text-white cursor-pointer">
                Temperature
                <div class="absolute bottom-0 left-0 w-full h-[3px] bg-[#fcc934] rounded-t-full"></div>
            </div>
            <div class="pb-2 hover:text-white cursor-pointer">Precipitation</div>
            <div class="pb-2 hover:text-white cursor-pointer">Wind</div>
        </div>

        {{-- Temperature Graph --}}
        <div class="relative h-32 w-full mt-8 px-6">
            {{-- Data Points --}}
            <div id="temp-points" class="absolute inset-0 flex justify-between px-2 text-[11px] text-white">
                {{-- Will be populated by JS --}}
            </div>

            <svg id="temp-graph" viewBox="0 0 800 100" class="w-full h-20 mt-4 overflow-visible">
                {{-- Will be populated by JS --}}
            </svg>

            {{-- Time Labels --}}
            <div id="time-labels" class="flex justify-between px-2 mt-4 text-[11px] text-[#9aa0a6]">
                {{-- Will be populated by JS --}}
            </div>
        </div>

        {{-- Forecast Carousel --}}
        <div class="flex gap-1 mt-6 px-6 pb-6 overflow-x-auto no-scrollbar">
            <div id="forecast-carousel">
                {{-- Will be populated by JS --}}
            </div>
        </div>
    </div>
</div>

<script>
function modernWeather() {
    return {
        isOpen: false,
        forecastData: [],
        
        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && this.forecastData.length === 0) {
                this.loadWeatherData();
            }
        },
        
        close() {
            this.isOpen = false;
        },
        
        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            this.loadWeatherData();
        },
        
        updateClock() {
            const timeEl = document.getElementById('current-time');
            if (timeEl) {
                const now = new Date();
                timeEl.textContent = now.toLocaleDateString('en-US', { 
                    weekday: 'long',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }
        },
        
        async loadWeatherData(city = 'Dasol') {
            try {
                const currentRes = await fetch(`/weather/current?city=${encodeURIComponent(city)}`);
                if (currentRes.ok) {
                    const currentData = await currentRes.json();
                    this.updateCurrentWeather(currentData);
                }
                
                const forecastRes = await fetch(`/weather/forecast?city=${encodeURIComponent(city)}`);
                if (forecastRes.ok) {
                    const forecastData = await forecastRes.json();
                    this.forecastData = forecastData.list;
                    this.updateGraph(forecastData.list);
                    this.updateCarousel(forecastData.list);
                }
            } catch (error) {
                console.error('Weather loading failed:', error);
            }
        },
        
        updateCurrentWeather(data) {
            const navbarTemp = document.getElementById('navbar-temp');
            if (navbarTemp && data.main) {
                navbarTemp.textContent = `${Math.round(data.main.temp)}°`;
            }
            
            const navbarIcon = document.getElementById('navbar-weather-icon');
            if (navbarIcon && data.weather?.[0]) {
                navbarIcon.textContent = this.getWeatherIcon(data.weather[0].main);
            }
            
            const mainIcon = document.getElementById('main-weather-icon');
            if (mainIcon && data.weather?.[0]) {
                mainIcon.textContent = this.getWeatherIcon(data.weather[0].main);
            }
            
            const mainTemp = document.getElementById('main-temp');
            if (mainTemp && data.main) {
                mainTemp.textContent = Math.round(data.main.temp);
            }
            
            const elements = {
                'precipitation': '0',
                'humidity': data.main?.humidity || '--',
                'wind': data.wind ? Math.round(data.wind.speed * 3.6) : '--',
                'weather-desc': data.weather?.[0]?.description || 'Loading...'
            };
            
            Object.entries(elements).forEach(([id, value]) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            });
        },
        
        updateGraph(forecastList) {
            const next8 = forecastList.slice(0, 8);
            const temps = next8.map(item => Math.round(item.main.temp));
            const times = next8.map(item => {
                const date = new Date(item.dt_txt);
                return date.toLocaleTimeString('en-US', { hour: 'numeric', hour12: true });
            });
            
            const maxTemp = Math.max(...temps);
            const minTemp = Math.min(...temps);
            const range = maxTemp - minTemp || 1;
            
            // Update temp points
            const pointsContainer = document.getElementById('temp-points');
            if (pointsContainer) {
                pointsContainer.innerHTML = temps.map((temp, i) => {
                    const y = ((maxTemp - temp) / range) * 60;
                    return `<span style="margin-top: ${y}px">${temp}</span>`;
                }).join('');
            }
            
            // Update graph
            const svg = document.getElementById('temp-graph');
            if (svg) {
                const points = temps.map((temp, i) => {
                    const x = (i / 7) * 800;
                    const y = ((maxTemp - temp) / range) * 80 + 10;
                    return `${x},${y}`;
                }).join(' L ');
                
                svg.innerHTML = `
                    <path d="M ${points}" fill="none" stroke="#fcc934" stroke-width="3" />
                    <path d="M ${points} V 100 H 0 Z" fill="rgba(252, 201, 52, 0.1)" />
                `;
            }
            
            // Update time labels
            const labelsContainer = document.getElementById('time-labels');
            if (labelsContainer) {
                labelsContainer.innerHTML = times.map(time => `<span>${time}</span>`).join('');
            }
        },
        
        updateCarousel(forecastList) {
            const dailyData = {};
            forecastList.forEach(item => {
                const date = new Date(item.dt_txt);
                const day = date.toLocaleDateString('en-US', { weekday: 'short' });
                if (!dailyData[day]) dailyData[day] = [];
                dailyData[day].push(item);
            });
            
            const carousel = document.getElementById('forecast-carousel');
            if (!carousel) return;
            
            carousel.innerHTML = '';
            
            Object.entries(dailyData).slice(0, 8).forEach(([day, entries], index) => {
                const temps = entries.map(e => e.main.temp);
                const maxTemp = Math.round(Math.max(...temps));
                const minTemp = Math.round(Math.min(...temps));
                const weather = entries[Math.floor(entries.length / 2)].weather[0];
                const icon = this.getWeatherIcon(weather.main);
                const color = this.getIconColor(weather.main);
                
                const isActive = index === 0;
                const bgClass = isActive ? 'bg-[#3c4043]' : 'hover:bg-[#303134]';
                
                carousel.innerHTML += `
                    <div class="flex-1 min-w-[80px] ${bgClass} rounded-xl p-3 flex flex-col items-center transition-colors cursor-pointer">
                        <span class="text-sm font-medium text-white mb-2">${day}</span>
                        <span class="material-symbols-outlined ${color} mb-2" style="font-variation-settings: 'FILL' 1">${icon}</span>
                        <div class="text-sm">
                            <span class="text-white ${isActive ? 'font-bold' : ''}">${maxTemp}°</span>
                            <span class="text-[#9aa0a6] ml-1">${minTemp}°</span>
                        </div>
                    </div>
                `;
            });
        },
        
        getWeatherIcon(weatherMain) {
            const iconMap = {
                'Clear': 'sunny',
                'Clouds': 'partly_cloudy_day',
                'Rain': 'rainy',
                'Drizzle': 'rainy',
                'Thunderstorm': 'thunderstorm',
                'Snow': 'cloudy_snowing',
                'Mist': 'foggy',
                'Fog': 'foggy'
            };
            return iconMap[weatherMain] || 'cloud';
        },
        
        getIconColor(weatherMain) {
            const colorMap = {
                'Clear': 'text-yellow-500',
                'Clouds': 'text-gray-400',
                'Rain': 'text-blue-400',
                'Drizzle': 'text-blue-400',
                'Thunderstorm': 'text-blue-400',
                'Snow': 'text-gray-400',
                'Mist': 'text-gray-400',
                'Fog': 'text-gray-400'
            };
            return colorMap[weatherMain] || 'text-gray-400';
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }

.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
