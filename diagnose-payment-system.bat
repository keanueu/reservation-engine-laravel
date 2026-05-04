@echo off
echo ========================================
echo   PAYMENT SYSTEM DIAGNOSTIC
echo ========================================
echo.

cd /d "%~dp0"

echo [1] Checking Queue Configuration...
echo ----------------------------------------
php artisan tinker --execute="echo 'Queue Connection: ' . config('queue.default');"
echo.

echo [2] Checking Pending Jobs...
echo ----------------------------------------
php artisan tinker --execute="echo 'Pending Jobs: ' . DB::table('jobs')->count();"
echo.

echo [3] Checking Failed Jobs...
echo ----------------------------------------
php artisan queue:failed
echo.

echo [4] Checking Recent Bookings Payment Status...
echo ----------------------------------------
php artisan tinker --execute="DB::table('bookings')->latest()->take(5)->get(['id', 'name', 'payment_status', 'status', 'group_id'])->each(function($b) { echo \"ID: {$b->id} | Name: {$b->name} | Payment: {$b->payment_status} | Status: {$b->status} | Group: {$b->group_id}\n\"; });"
echo.

echo [5] Checking Webhook Route...
echo ----------------------------------------
php artisan route:list | findstr "webhooks/paymongo"
echo.

echo [6] Checking Mail Configuration...
echo ----------------------------------------
php artisan tinker --execute="echo 'Mail Driver: ' . config('mail.default') . '\n'; echo 'Mail Host: ' . config('mail.mailers.smtp.host') . '\n'; echo 'Mail Port: ' . config('mail.mailers.smtp.port');"
echo.

echo [7] Checking Recent Webhook Logs...
echo ----------------------------------------
powershell -Command "Get-Content storage\logs\laravel.log -Tail 100 | Select-String -Pattern '\[Webhook\]' | Select-Object -Last 10"
echo.

echo [8] Checking PayMongo Configuration...
echo ----------------------------------------
php artisan tinker --execute="echo 'PayMongo Secret: ' . (env('PAYMONGO_SECRET') ? 'SET (sk_test_...)' : 'NOT SET') . '\n'; echo 'Webhook Secret: ' . (env('PAYMONGO_WEBHOOK_SECRET') ? 'SET (whsk_...)' : 'NOT SET') . '\n'; echo 'Success URL: ' . env('PAYMONGO_SUCCESS_URL');"
echo.

echo ========================================
echo   DIAGNOSTIC COMPLETE
echo ========================================
echo.
echo NEXT STEPS:
echo 1. If "Pending Jobs" ^> 0: Start queue worker with start-queue-worker.bat
echo 2. If no webhook logs: Register webhook in PayMongo Dashboard
echo 3. If payment_status = 'pending': Webhook not being called
echo.
pause
