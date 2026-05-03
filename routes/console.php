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

// Release expired pending/waiting bookings every minute so rooms free up instantly
Schedule::command('bookings:cleanup-abandoned')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/cleanup-abandoned.log'));

// Mark checked-out bookings as completed every 15 minutes
Schedule::command('bookings:release')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground();
