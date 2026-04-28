<?php
// Usage: php scripts/update_payment_id.php <booking_id> <paymongo_payment_id>
// Example: php scripts/update_payment_id.php 123 pay_ABC123XYZ

require __DIR__ . '/../vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

// Bootstrap Laravel DB (for standalone script)
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'hotel', // <-- CHANGE THIS
    'username'  => 'root', // <-- CHANGE IF NEEDED
    'password'  => '', // <-- CHANGE IF NEEDED
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

if ($argc !== 3) {
    echo "Usage: php update_payment_id.php <booking_id> <paymongo_payment_id>\n";
    exit(1);
}

$bookingId = $argv[1];
$paymentId = $argv[2];

$booking = Capsule::table('bookings')->where('id', $bookingId)->first();
if (!$booking) {
    echo "Booking not found.\n";
    exit(1);
}

Capsule::table('bookings')->where('id', $bookingId)->update(['payment_id' => $paymentId]);
echo "Updated booking #$bookingId with payment_id $paymentId.\n";
