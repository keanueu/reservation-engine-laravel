<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\WeatherAlertController;
use Illuminate\Http\Request;

echo "--- Testing Typhoon Alert API ---\n";
$controller = new WeatherAlertController();
$response = $controller->checkTyphoonStatus();

echo "Status: " . $response->getStatusCode() . "\n";
echo "Body: " . $response->getContent() . "\n";
