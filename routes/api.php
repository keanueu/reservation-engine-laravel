<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// Controllers
use App\Http\Controllers\PaymongoWebhookController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| AUTH Sanctum User
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| PUBLIC WEBHOOKS
|--------------------------------------------------------------------------
*/
// PayMongo MUST be public (gateway must reach it)
Route::post('/paymongo/webhook', [PaymongoWebhookController::class, 'handle']);


/*
|--------------------------------------------------------------------------
| ALERTS API (THIS IS WHAT YOUR alert.js USES)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/alerts/current', [AlertController::class, 'current'])->name('api.alerts.current');
    Route::get('/user/notifications', [AlertController::class, 'userNotifications'])->name('api.user.notifications');
    Route::post('/user/notifications/{id}/read', [AlertController::class, 'markNotificationRead'])->name('api.user.notifications.read');
});



/*
|--------------------------------------------------------------------------
| BOOKING API SAMPLE
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/booking/create', [BookingController::class, 'store']);
    Route::get('/booking/list', [BookingController::class, 'list']);
});
