@echo off
echo ========================================
echo   PayMongo Webhook Fix Deployment
echo ========================================
echo.
echo This script will:
echo 1. Backup current webhook controller
echo 2. Deploy optimized version
echo 3. Clear caches
echo 4. Restart queue workers
echo.
echo ========================================
echo.

REM Step 1: Backup
echo [1/4] Creating backup...
if exist app\Http\Controllers\PaymongoWebhookController.php (
    copy app\Http\Controllers\PaymongoWebhookController.php app\Http\Controllers\PaymongoWebhookController_BACKUP_%date:~-4,4%%date:~-10,2%%date:~-7,2%.php
    echo Backup created successfully
) else (
    echo WARNING: Original file not found
)
echo.

REM Step 2: Deploy
echo [2/4] Deploying optimized webhook controller...
if exist app\Http\Controllers\PaymongoWebhookController_OPTIMIZED.php (
    copy /Y app\Http\Controllers\PaymongoWebhookController_OPTIMIZED.php app\Http\Controllers\PaymongoWebhookController.php
    echo Deployment successful
) else (
    echo ERROR: Optimized file not found
    pause
    exit /b 1
)
echo.

REM Step 3: Clear caches
echo [3/4] Clearing caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo Caches cleared
echo.

REM Step 4: Restart queue
echo [4/4] Restarting queue workers...
php artisan queue:restart
echo Queue workers restarted
echo.

echo ========================================
echo   Deployment Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Test webhook: curl -X POST https://yourdomain.com/webhooks/paymongo
echo 2. Check logs: tail -f storage/logs/laravel.log
echo 3. Monitor PayMongo dashboard
echo.
echo See WEBHOOK_FIX_COMPLETE_GUIDE.md for full documentation
echo.
pause
