@echo off
echo ========================================
echo   WEBHOOK 200 OK TEST
echo ========================================
echo.
echo Testing if webhook ALWAYS returns 200 OK...
echo.

cd /d "%~dp0"

echo Test 1: Valid webhook call
echo ----------------------------------------
curl -i -X POST http://localhost/webhooks/paymongo ^
  -H "Content-Type: application/json" ^
  -H "Paymongo-Signature: t=1234567890,te=validtestsignature" ^
  -d "{\"data\":{\"attributes\":{\"type\":\"checkout_session.payment.paid\",\"data\":{\"id\":\"cs_test123\",\"attributes\":{\"metadata\":{\"group_id\":\"test-group-123\"}}}}}}"
echo.
echo Expected: HTTP/1.1 200 OK
echo.
pause

echo Test 2: Invalid signature (should still return 200)
echo ----------------------------------------
curl -i -X POST http://localhost/webhooks/paymongo ^
  -H "Content-Type: application/json" ^
  -H "Paymongo-Signature: t=123,te=invalidsignature" ^
  -d "{\"data\":{\"attributes\":{\"type\":\"test\"}}}"
echo.
echo Expected: HTTP/1.1 200 OK (NOT 400!)
echo.
pause

echo Test 3: Invalid JSON (should still return 200)
echo ----------------------------------------
curl -i -X POST http://localhost/webhooks/paymongo ^
  -H "Content-Type: application/json" ^
  -H "Paymongo-Signature: t=123,te=test" ^
  -d "invalid json here"
echo.
echo Expected: HTTP/1.1 200 OK (NOT 400!)
echo.
pause

echo Test 4: Missing signature header (should still return 200)
echo ----------------------------------------
curl -i -X POST http://localhost/webhooks/paymongo ^
  -H "Content-Type: application/json" ^
  -d "{\"data\":{\"attributes\":{\"type\":\"test\"}}}"
echo.
echo Expected: HTTP/1.1 200 OK (NOT 400!)
echo.
pause

echo ========================================
echo   TEST COMPLETE
echo ========================================
echo.
echo IMPORTANT: All tests should return "HTTP/1.1 200 OK"
echo.
echo If ANY test returns 400 or 500:
echo   1. Clear cache: php artisan config:clear
echo   2. Restart web server
echo   3. Run this test again
echo.
echo Check logs for details:
echo   type storage\logs\laravel.log ^| findstr /i "webhook"
echo.
pause
