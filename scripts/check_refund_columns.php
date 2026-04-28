<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = ['refund_status','refund_amount','refund_reason','refunded_at'];
foreach ($columns as $c) {
    $exists = Illuminate\Support\Facades\Schema::hasColumn('bookings', $c) ? 'yes' : 'no';
    echo $c . ': ' . $exists . PHP_EOL;
}
