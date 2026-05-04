@echo off
echo ========================================
echo   WEBHOOK ENDPOINT TEST
echo ========================================
echo.
echo Testing if webhook endpoint is accessible...
echo.

cd /d "%~dp0"

echo Test 1: Check if route exists
php artisan route:list | findstr "webhooks/paymongo"
echo.

echo Test 2: Send test POST request
curl -X POST http://localhost/webhooks/paymongo -H "Content-Type: application/json" -H "Paymongo-Signature: t=123,te=test" -d "{\"data\":{\"attributes\":{\"type\":\"test\"}}}"
echo.
echo.

echo Test 3: Check recent logs for webhook activity
powershell -Command "Get-Content storage\logs\laravel.log -Tail 50 | Select-String -Pattern 'webhook|Webhook'"
echo.

echo ========================================
echo   TEST COMPLETE
echo ========================================
echo.
echo If you see webhook logs above, the endpoint works!
echo If not, check:
echo   1. Web server is running (Apache/nginx)
echo   2. Route is registered in routes/web.php
echo   3. CSRF is excluded in bootstrap/app.php
echo.
pause
