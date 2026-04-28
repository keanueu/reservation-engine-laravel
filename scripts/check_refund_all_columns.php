<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = [
    'refund_status',
    'refund_requested_amount',
    'refund_fee',
    'refund_amount',
    'refund_reason',
    'refunded_at',
];

foreach ($columns as $c) {
    $exists = Illuminate\Support\Facades\Schema::hasColumn('bookings', $c) ? 'yes' : 'no';
    echo str_pad($c, 30) . ': ' . $exists . PHP_EOL;
}
