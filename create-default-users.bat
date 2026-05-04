@echo off
echo ========================================
echo   Creating Default Users
echo ========================================
echo.
echo This will create:
echo - Admin: admin@cabanas.com / admin123
echo - Frontdesk: frontdesk@cabanas.com / frontdesk123
echo - User: user@cabanas.com / user123
echo.
echo ========================================
echo.

php artisan db:seed --class=DefaultUsersSeeder

echo.
echo ========================================
echo   Done!
echo ========================================
echo.
echo You can now login with the credentials above.
echo.
pause
