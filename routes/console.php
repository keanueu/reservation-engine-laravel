<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\WeatherAlertController;

Artisan::command('typhoon:check', function () {
    $controller = new WeatherAlertController();
    $response = $controller->checkTyphoonStatus()->getData();

    if ($response->status === 'warning') {
        Log::warning('Typhoon Alert: ' . $response->event);
    }
})->describe('Check for typhoon alerts');

// Schedule cleanup of abandoned bookings every 10 minutes
Schedule::command('bookings:cleanup-abandoned')->everyTenMinutes();

// Schedule release of completed bookings every hour
Schedule::command('bookings:release')->hourly();
