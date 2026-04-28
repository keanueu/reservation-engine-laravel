<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\PaymongoService;
use Illuminate\Support\Facades\Http;

echo "--- Testing PayMongo API ---\n";
$paymongo = new PaymongoService();
$result = $paymongo->createLink(10000, 'Test Link', ['test' => true]);

if ($result['success']) {
    echo "PayMongo Link Creation: SUCCESS\n";
    echo "Link ID: " . $result['link_id'] . "\n";
    echo "Checkout URL: " . $result['checkout_url'] . "\n";
} else {
    echo "PayMongo Link Creation: FAILED\n";
    echo "Message: " . (is_array($result['message']) ? json_encode($result['message']) : $result['message']) . "\n";
    if (isset($result['status_code'])) {
        echo "Status Code: " . $result['status_code'] . "\n";
    }
}

echo "\n--- Testing OpenWeather API ---\n";
$weatherApiKey = env('OPENWEATHER_API_KEY');
if ($weatherApiKey) {
    $lat = 15.9778;
    $lon = 119.7769;
    $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
        'lat' => $lat,
        'lon' => $lon,
        'appid' => $weatherApiKey,
        'units' => 'metric'
    ]);
    
    if ($response->successful()) {
        echo "OpenWeather API: SUCCESS\n";
        echo "Current Temp: " . $response->json()['main']['temp'] . "°C\n";
    } else {
        echo "OpenWeather API: FAILED\n";
        echo "Status: " . $response->status() . "\n";
        echo "Body: " . $response->body() . "\n";
    }
} else {
    echo "OpenWeather API: NO KEY CONFIGURED\n";
}

echo "\n--- Testing Local AI (Ollama) ---\n";
try {
    $response = Http::timeout(5)->post('http://localhost:11434/api/tags');
    if ($response->successful()) {
        echo "Ollama Local API: SUCCESS (Service is running)\n";
        $models = array_column($response->json()['models'] ?? [], 'name');
        echo "Available Models: " . implode(', ', $models) . "\n";
    } else {
        echo "Ollama Local API: FAILED (Status: " . $response->status() . ")\n";
    }
} catch (\Exception $e) {
    echo "Ollama Local API: FAILED (Could not connect: " . $e->getMessage() . ")\n";
}
