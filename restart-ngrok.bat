@echo off
echo ========================================
echo   Quick Fix: Restart Ngrok Tunnel
echo ========================================
echo.

REM Step 1: Kill existing ngrok
echo [1/3] Stopping existing ngrok tunnel...
taskkill /F /IM ngrok.exe 2>nul
if %errorlevel% equ 0 (
    echo Existing tunnel stopped
) else (
    echo No existing tunnel found
)
echo.

REM Step 2: Start new ngrok
echo [2/3] Starting new ngrok tunnel...
echo.
echo Choose your setup:
echo 1. XAMPP (port 80)
echo 2. Laravel Artisan Serve (port 8000)
echo.
set /p choice="Enter choice (1 or 2): "

if "%choice%"=="1" (
    echo Starting ngrok on port 80...
    start cmd /k "ngrok http 80"
) else if "%choice%"=="2" (
    echo Starting ngrok on port 8000...
    start cmd /k "ngrok http 8000"
) else (
    echo Invalid choice. Starting on port 80 by default...
    start cmd /k "ngrok http 80"
)

echo.
echo Waiting for ngrok to start...
timeout /t 3 /nobreak >nul
echo.

REM Step 3: Instructions
echo [3/3] Next steps:
echo ========================================
echo.
echo 1. Look at the ngrok window that just opened
echo 2. Find the line that says "Forwarding"
echo 3. Copy the HTTPS URL (e.g., https://abc123.ngrok-free.app)
echo.
echo 4. Update PayMongo webhook:
echo    - Go to: https://dashboard.paymongo.com/developers/webhooks
echo    - Click on your webhook
echo    - Update URL to: https://YOUR-NEW-URL.ngrok-free.app/webhooks/paymongo
echo    - Click Save
echo.
echo 5. Test your webhook:
echo    - In PayMongo dashboard, click "Send Test Event"
echo    - Check your Laravel logs: storage/logs/laravel.log
echo.
echo ========================================
echo.
echo TIP: To avoid this issue in the future:
echo - Use ngrok static domain (see FIX_NGROK_TUNNEL_OFFLINE.md)
echo - Or deploy to Railway.app for permanent URL
echo.
echo Press any key to open ngrok web interface (http://localhost:4040)
pause >nul

start http://localhost:4040

echo.
echo Done! Check the ngrok window for your new URL.
echo.
pause
