# PayMongo Payment Issue - Fix Summary

## Issue Identified
When booking rooms, the system was not redirecting to PayMongo payment page.

## Root Cause
In `BookingController.php` line 237, the redirect was using `redirect()->away()` for a local route, which is incorrect.

```php
// BEFORE (INCORRECT):
return redirect()->away(
    route('bookings.pay', ['booking' => $bookings[0]->id]) .
    '?group_id=' . $groupId .
    '&amount=' . $totalToCharge
);

// AFTER (FIXED):
return redirect()->route('bookings.pay', [
    'booking' => $bookings[0]->id,
    'group_id' => $groupId,
    'amount' => $totalToCharge
]);
```

## What Was Wrong
- `redirect()->away()` is meant for external URLs only
- Using it with `route()` helper caused the redirect to fail
- Query parameters were being manually concatenated instead of using Laravel's route parameter array

## Fix Applied
Changed to use `redirect()->route()` with proper parameter array, which:
- Correctly handles local route redirects
- Properly formats query parameters
- Follows Laravel best practices

## Payment Flow
1. User fills checkout form → submits to `bookings.store`
2. BookingController creates bookings with `payment_status = 'pending'`
3. Redirects to `bookings.pay` route with group_id and amount
4. PaymentController creates PayMongo link
5. Redirects to PayMongo checkout URL
6. After payment, PayMongo webhook updates booking status to 'paid'

## Environment Check
✓ PAYMONGO_SECRET is configured: `sk_test_VpBtNLRwG5esis6uWd5xmxGs`
✓ Webhook secret is configured
✓ Success/Cancel URLs are set

## Testing Steps
1. Add rooms to cart
2. Go to checkout
3. Fill in contact details
4. Click "Confirm & Book Now"
5. Should now redirect to PaymentController
6. PaymentController should create PayMongo link
7. Should redirect to PayMongo payment page

## Additional Notes
- Minimum booking amount: ₱100
- Deposit percentage: Configurable via Settings (default 50%)
- Payment ID is saved to bookings for webhook processing
