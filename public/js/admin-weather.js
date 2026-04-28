
// Use server-side proxy endpoints to keep API key secret
const API_CURRENT = '/weather/current';
const API_FORECAST = '/weather/forecast';

let city = localStorage.getItem('weather_city') || 'Dasol';
const currentDateEl = document.getElementById('current-date');
const currentTimeEl = document.getElementById('current-time');
const cityInputEl = document.getElementById('city-input');
const citySearchEl = document.getElementById('city-search');

if (!currentDateEl || !currentTimeEl || !cityInputEl || !citySearchEl) {
    console.warn('admin-weather.js loaded without weather UI present; skipping initialization.');
} else {

// Live date/time
function updateClock() {
    currentDateEl.textContent = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' });
    currentTimeEl.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
}
setInterval(updateClock, 1000);
updateClock();

function formatTime(timestamp) {
    const date = new Date(timestamp * 1000);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
}

async function fetchCurrentWeather(cityName) {
    try {
        const res = await fetch(`${API_CURRENT}?city=${encodeURIComponent(cityName)}`);
        if (!res.ok) throw new Error('Network error');
        const data = await res.json();
        if (data && data.name) updateCurrentWeatherUI(data);
        else console.warn('Current weather: unexpected response', data);
    } catch (err) {
        console.error('fetchCurrentWeather', err);
    }
}

async function fetchForecast(cityName) {
    try {
        const res = await fetch(`${API_FORECAST}?city=${encodeURIComponent(cityName)}`);
        if (!res.ok) throw new Error('Network error');
        const data = await res.json();
        if (data && data.list) updateForecastUI(data.list);
        else console.warn('Forecast: unexpected response', data);
    } catch (err) {
        console.error('fetchForecast', err);
    }
}

function updateCurrentWeatherUI(data) {
    document.getElementById('current-city').textContent = data.name || city;
    // Clear country if not available
    document.getElementById('current-country').textContent = (data.sys && data.sys.country) ? data.sys.country : '';

    if (data.main) {
        document.getElementById('current-temp').textContent = Math.round(data.main.temp);
        document.getElementById('high-temp').textContent = `${Math.round(data.main.temp_max)}°`;
        document.getElementById('low-temp').textContent = `${Math.round(data.main.temp_min)}°`;
        document.getElementById('humidity-value').textContent = (data.main.humidity ?? '-') + '%';
        document.getElementById('pressure-value').textContent = (data.main.pressure ?? '-') + ' hPa';
        document.getElementById('feels-like-value').textContent = Math.round(data.main.feels_like || 0) + '°C';
    }

    if (data.weather && data.weather[0]) {
        document.getElementById('current-description').textContent = data.weather[0].description;
    }

    if (typeof data.visibility !== 'undefined') {
        document.getElementById('visibility-value').textContent = (data.visibility / 1000).toFixed(1) + ' km';
    }

    if (data.wind) {
        document.getElementById('wind-speed-value').textContent = (data.wind.speed ?? '-') + ' m/s';
    }

    if (data.sys) {
        if (data.sys.sunrise) document.getElementById('sunrise-time').textContent = formatTime(data.sys.sunrise);
        if (data.sys.sunset) document.getElementById('sunset-time').textContent = formatTime(data.sys.sunset);
    }
}

function updateForecastUI(list) {
    const forecastContainer = document.getElementById('forecast-list');
    forecastContainer.innerHTML = '';

    const dailyData = {};
    list.forEach(item => {
        const date = new Date(item.dt_txt).toLocaleDateString('en-US', { weekday: 'short' });
        if (!dailyData[date]) dailyData[date] = [];
        dailyData[date].push(item);
    });

    Object.entries(dailyData).slice(0, 5).forEach(([day, entries]) => {
        const temps = entries.map(e => e.main.temp);
        const avgTemp = Math.round(temps.reduce((a, b) => a + b, 0) / temps.length);
        const weather = entries[Math.floor(entries.length / 2)].weather[0];

        const itemHTML = `
                    <div class="forecast-item p-1 bg-white/50 dark:bg-gray-800/25 hover:bg-gray-100 dark:hover:bg-gray-700/20 transition rounded-md text-gray-900 dark:text-white flex justify-between items-center">
                        <div class="flex items-center">
                            <img src="https://openweathermap.org/img/wn/${weather.icon}.png" class="w-4 h-4 mr-3" />
                            <div>
                                <p class="text-xs font-medium text-gray-900 dark:text-white">${day}</p>
                                <p class="text-xs text-gray-600 dark:text-white/70">${weather.description}</p>
                            </div>
                        </div>
                        <p class="text-xs font-light text-gray-900 dark:text-white">${avgTemp}°</p>
                    </div>
                `;
        forecastContainer.insertAdjacentHTML('beforeend', itemHTML);
    });
}

// Wire up city input
function setCity(newCity) {
    city = newCity || city;
    localStorage.setItem('weather_city', city);
    cityInputEl.value = city;
    fetchCurrentWeather(city);
    fetchForecast(city);
}

citySearchEl.addEventListener('click', () => {
    const v = cityInputEl.value.trim();
    if (v) setCity(v);
});
cityInputEl.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        const v = e.target.value.trim();
        if (v) setCity(v);
    }
});

// initial value
cityInputEl.value = city;
setCity(city);

// Auto-refresh current weather every 10 minutes and forecast every 30 minutes
setInterval(() => {
    if (!document.hidden) {
        fetchCurrentWeather(city);
    }
}, 10 * 60 * 1000);
setInterval(() => {
    if (!document.hidden) {
        fetchForecast(city);
    }
}, 30 * 60 * 1000);

}

