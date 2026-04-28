<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherAlertController extends Controller
{
    /**
     * Checks for potential severe weather by querying the OpenWeather Current Weather API.
     * NOTE: The 'alerts' feature is generally restricted to paid plans. This function
     * uses the free-tier 'weather' endpoint and checks the current description for keywords.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTyphoonStatus()
    {
        $payload = Cache::remember('weather_alert_typhoon_status', 60, function () {
            // Coordinates for Cabanas Beach Resort, Tambobong Dasol Pangasinan
            $lat = 15.9778;
            $lon = 119.7769;
            $apiKey = env('OPENWEATHER_API_KEY');

            // Changed from the restricted 'onecall' to the free-tier 'weather' endpoint
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $apiKey,
                'units' => 'metric',
                'lang' => 'en'
            ]);

            if ($response->failed()) {
                $statusCode = $response->status();
                $errorMessage = "OpenWeather request failed (Status: {$statusCode}).";

                if ($statusCode === 401) {
                    // The most common reason: free API keys are restricted from using 'onecall' or similar features.
                    $errorMessage = 'Authentication error (401). Check API key and ensure it has access to the /weather endpoint.';
                } elseif ($statusCode === 429) {
                    $errorMessage = 'Rate Limit exceeded (429). You have made too many requests to the OpenWeather API.';
                }

                Log::error('Weather API request failed', [
                    'status' => $statusCode,
                    'body' => $response->body()
                ]);

                return [
                    'http_status' => $statusCode,
                    'body' => [
                        'status' => 'error',
                        'message' => $errorMessage,
                    ],
                ];
            }

            $data = $response->json();

            // Check the current weather description for typhoon/storm keywords (fallback for free tier)
            $weatherDescription = strtolower($data['weather'][0]['description'] ?? '');
            $mainWeather = $data['weather'][0]['main'] ?? 'N/A';
            $currentTemp = $data['main']['temp'] ?? 'N/A';
            $windSpeed = $data['wind']['speed'] ?? 'N/A';

            if (str_contains($weatherDescription, 'typhoon') || str_contains($weatherDescription, 'cyclone') || str_contains($weatherDescription, 'storm')) {
                return [
                    'http_status' => 200,
                    'body' => [
                        'status' => 'warning',
                        'location' => 'Tambobong, Dasol, Pangasinan',
                        'event' => 'Potential Severe Weather Alert (Based on Current Conditions)',
                        'description' => "Current conditions indicate: {$mainWeather} ({$weatherDescription}). Please stay safe and monitor PAGASA updates.",
                        'temp' => "{$currentTemp}°C",
                        'wind_speed' => "{$windSpeed} m/s",
                    ],
                ];
            }

            return [
                'http_status' => 200,
                'body' => [
                    'status' => 'clear',
                    'location' => 'Tambobong, Dasol, Pangasinan',
                    'message' => "No immediate severe weather reported near Cabanas Beach Resort (Current: {$mainWeather}).",
                ],
            ];
        });

        return response()->json($payload['body'], $payload['http_status']);
    }
}
