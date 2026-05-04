@echo off
title Ngrok Auto-Restart - Keep Alive
color 0A

echo ========================================
echo   Ngrok Auto-Restart Service
echo ========================================
echo.
echo This will keep ngrok running and auto-restart if it crashes.
echo Press Ctrl+C to stop.
echo.
echo ========================================
echo.

REM Configuration
set PORT=80
set DOMAIN=

REM Ask for port
echo What port is your application running on?
echo 1. Port 80 (XAMPP)
echo 2. Port 8000 (Laravel Artisan)
echo.
set /p port_choice="Enter choice (1 or 2): "

if "%port_choice%"=="2" (
    set PORT=8000
)

echo.
echo Do you have a static ngrok domain? (y/n)
set /p has_domain="Enter choice: "

if /i "%has_domain%"=="y" (
    echo.
    echo Enter your ngrok static domain (e.g., your-app.ngrok-free.app):
    set /p DOMAIN="Domain: "
)

echo.
echo ========================================
echo   Configuration
echo ========================================
echo Port: %PORT%
if defined DOMAIN (
    echo Domain: %DOMAIN%
    echo Command: ngrok http %PORT% --domain=%DOMAIN%
) else (
    echo Domain: Random (changes each restart)
    echo Command: ngrok http %PORT%
)
echo ========================================
echo.
echo Starting in 3 seconds... (Press Ctrl+C to cancel)
timeout /t 3 /nobreak >nul

:start
echo.
echo [%date% %time%] Starting ngrok tunnel...
echo.

if defined DOMAIN (
    ngrok http %PORT% --domain=%DOMAIN%
) else (
    ngrok http %PORT%
)

echo.
echo [%date% %time%] Ngrok stopped. Restarting in 5 seconds...
echo.
timeout /t 5 /nobreak >nul

goto start
