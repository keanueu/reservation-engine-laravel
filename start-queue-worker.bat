@echo off
echo ========================================
echo   LARAVEL QUEUE WORKER - MAIL PROCESSOR
echo ========================================
echo.
echo This window MUST stay open for emails to send!
echo Press Ctrl+C to stop the worker.
echo.
echo Starting queue worker...
echo.

cd /d "%~dp0"

:loop
php artisan queue:work --queue=mail,default --tries=3 --timeout=90 --sleep=3 --verbose
echo.
echo Queue worker stopped. Restarting in 5 seconds...
timeout /t 5
goto loop
