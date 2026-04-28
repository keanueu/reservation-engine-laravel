<?php

return [
    // Percentage of the total that should be collected at booking time (deposit)
    // Example: 50 means 50% of the total_amount will be charged now.
    'deposit_percentage' => env('BOOKING_DEPOSIT_PERCENTAGE', 50),
    // Percentage fee added to deposits at booking time (not used by Cabanas business rule).
    // Set to 0 to disable adding any booking-time fee. The refund fee (5%) is applied
    // only when processing refunds and is handled elsewhere in the controllers.
    'deposit_fee_percentage' => env('BOOKING_DEPOSIT_FEE_PERCENTAGE', 0),
    // When true, attempt to refund via configured payment gateway (PayMongo).
    // Set `REFUND_VIA_GATEWAY=true` in .env to enable. Default: false (mark refunds locally).
    'refund_via_gateway' => env('REFUND_VIA_GATEWAY', false),
];
