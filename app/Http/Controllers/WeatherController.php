<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    // Return current weather for given city (proxied)
    public function current(Request $request)
    {
        $city = $request->query('city', 'Dasol');
        $apiKey = env('OPENWEATHER_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'OPENWEATHER_API_KEY not configured'], 500);
        }

        $cacheKey = 'weather_current_' . strtolower($city);

        $data = Cache::remember($cacheKey, 600, function () use ($city, $apiKey) {
            $url = 'https://api.openweathermap.org/data/2.5/weather';
            $resp = Http::get($url, [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);
            return $resp->json();
        });

        return response()->json($data);
    }

    // Return 5-day forecast for given city (proxied)
    public function forecast(Request $request)
    {
        $city = $request->query('city', 'Dasol');
        $apiKey = env('OPENWEATHER_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'OPENWEATHER_API_KEY not configured'], 500);
        }

        $cacheKey = 'weather_forecast_' . strtolower($city);

        $data = Cache::remember($cacheKey, 600, function () use ($city, $apiKey) {
            $url = 'https://api.openweathermap.org/data/2.5/forecast';
            $resp = Http::get($url, [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);
            return $resp->json();
        });

        return response()->json($data);
    }
}
