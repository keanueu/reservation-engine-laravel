<?php
// Simple script to test OpenWeather API using values from .env
// Usage: php scripts/check_openweather.php

function readEnv($path)
{
    if (!file_exists($path)) {
        return [];
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $val) = explode('=', $line, 2);
        $data[trim($key)] = trim($val);
    }
    return $data;
}

$envPath = __DIR__ . '/../.env';
$env = readEnv($envPath);

$apiKey = $env['OPENWEATHER_API_KEY'] ?? null;
$baseUrl = $env['OPENWEATHER_BASE_URL'] ?? 'https://api.openweathermap.org/data/2.5/onecall';
$units = $env['OPENWEATHER_UNITS'] ?? 'metric';
$exclude = $env['OPENWEATHER_EXCLUDE'] ?? null;

if (!$apiKey) {
    echo "OPENWEATHER_API_KEY not found in .env\n";
    exit(1);
}

$lat = '15.9778';
$lon = '119.7769';

$query = [
    'lat' => $lat,
    'lon' => $lon,
    'appid' => $apiKey,
    'units' => $units,
    'lang' => 'en',
];
if (!empty($exclude)) $query['exclude'] = $exclude;

$url = $baseUrl . '?' . http_build_query($query);

// Mask the key for output
$maskedQuery = $query;
$maskedQuery['appid'] = '***masked***';

echo "Requesting: $baseUrl\n";
echo "Query: " . json_encode($maskedQuery) . "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

echo "HTTP status: $httpCode\n";
if ($err) {
    echo "cURL error: $err\n";
    exit(2);
}

// Try to pretty-print JSON if possible
$decoded = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    echo $response . "\n";
}

// Exit with 0 when 200, otherwise non-zero
exit(($httpCode >= 200 && $httpCode < 300) ? 0 : 3);
