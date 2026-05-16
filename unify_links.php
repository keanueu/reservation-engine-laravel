<?php

$replacements = [
    [
        'file' => 'resources/views/home/sections/room.blade.php',
        'search' => "onclick=\"location.href='{{ route('booking.dates', ['room_id' => \$room->id, 'type' => 'room']) }}'\"",
        'replace' => "onclick=\"openBookingModal('{{ \$room->id }}', '{{ addslashes(\$room->room_name) }}', {{ \$room->price }}, {{ (int)\$room->accommodates }})\""
    ],
    [
        'file' => 'resources/views/home/room_detailsv2.blade.php',
        'search' => 'href="{{ route(\'booking.dates\') }}"',
        'replace' => 'href="javascript:void(0)" onclick="openBookingModal()"'
    ],
    [
        'file' => 'resources/views/home/partials/nav.blade.php',
        'search' => 'href="{{ route(\'booking.dates\') }}"',
        'replace' => 'href="javascript:void(0)" onclick="openBookingModal()"'
    ],
    [
        'file' => 'resources/views/home/my-bookings.blade.php',
        'search' => 'href="{{ route(\'booking.dates\') }}"',
        'replace' => 'href="javascript:void(0)" onclick="openBookingModal()"'
    ],
    [
        'file' => 'resources/views/home/cart/room-tab.blade.php',
        'search' => "onclick=\"location.href='{{ route('booking.dates', ['room_id' => \$room->id, 'type' => 'room']) }}'\"",
        'replace' => "onclick=\"openBookingModal('{{ \$room->id }}', '{{ addslashes(\$room->room_name) }}', {{ \$room->price }}, {{ (int)\$room->accommodates }})\""
    ],
    [
        'file' => 'resources/views/home/cart/index.blade.php',
        'search' => 'href="{{ route(\'booking.dates\') }}"',
        'replace' => 'href="javascript:void(0)" onclick="openBookingModal()"'
    ],
    [
        'file' => 'resources/views/home/booking/step-guests.blade.php',
        'search' => 'href="{{ route(\'booking.dates\') }}"',
        'replace' => 'href="javascript:void(0)" onclick="openBookingModal()"'
    ]
];

foreach ($replacements as $rep) {
    $path = __DIR__ . '/' . $rep['file'];
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $content = str_replace($rep['search'], $rep['replace'], $content);
        file_put_contents($path, $content);
        echo "Updated: {$rep['file']}\n";
    }
}
