<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete your booking</title>
    <style>
        body { margin: 0; padding: 0; background: #f5f5f5; font-family:'Inter', Arial, sans-serif; color: #111827; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border: 1px solid #e5e7eb; }
        .header { background: #111827; padding: 32px 40px; text-align: center; }
        .header h1 { margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.05em; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,0.6); font-size: 13px; }
        .urgency-banner { background: #fef2f2; border-bottom: 3px solid #dc2626; padding: 20px 40px; text-align: center; }
        .urgency-banner .timer { font-size: 36px; font-weight: 700; color: #dc2626; line-height: 1; }
        .urgency-banner .timer-label { font-size: 12px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #dc2626; margin-top: 4px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; margin-bottom: 20px; }
        .section-label { font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #6b7280; margin-bottom: 10px; }
        .booking-card { border: 1px solid #e5e7eb; padding: 16px 20px; margin-bottom: 12px; }
        .booking-card .room-name { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 8px; }
        .booking-card .detail-row { display: flex; justify-content: space-between; font-size: 13px; color: #4b5563; margin-bottom: 4px; }
        .booking-card .detail-row span:last-child { font-weight: 600; color: #111827; }
        .cta-section { text-align: center; margin: 32px 0; }
        .cta-btn { display: inline-block; background: #964B00; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 16px 40px; }
        .warning-note { background: #fffbeb; border: 1px solid #fde68a; padding: 14px 18px; font-size: 13px; color: #92400e; margin-bottom: 24px; line-height: 1.5; }
        .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
        .footer a { color: #964B00; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- Header --}}
    <div class="header">
        <h1>Cabanas Beach Resort</h1>
        <p>Tambobong, Dasol, Pangasinan</p>
    </div>

    {{-- Urgency banner --}}
    <div class="urgency-banner">
        <div class="timer">{{ $minutesLeft }} min</div>
        <div class="timer-label">until your hold expires</div>
    </div>

    <div class="body">
        @php
            $guestName = $booking->name ?? 'Guest';
        @endphp

        <div class="greeting">Hi <strong>{{ $guestName }}</strong>,</div>

        <div class="warning-note">
            ⚠️ Your room hold is about to expire. If you don't complete your payment within
            <strong>{{ $minutesLeft }} minutes</strong>, your reservation will be released and
            the room may be booked by someone else.
        </div>

        {{-- Booking summary --}}
        <div class="section-label">Your reservation</div>

        <div class="booking-card">
            @if(isset($booking->room_id))
                <div class="room-name">
                    {{ optional($booking->room)->room_name ?? 'Room Booking #' . $booking->id }}
                </div>
                <div class="detail-row">
                    <span>Check-in</span>
                    <span>{{ \Carbon\Carbon::parse($booking->start_date)->format('M j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span>Check-out</span>
                    <span>{{ \Carbon\Carbon::parse($booking->end_date)->format('M j, Y') }}</span>
                </div>
            @else
                <div class="room-name">
                    {{ optional($booking->boat)->name ?? 'Boat Booking #' . $booking->id }}
                </div>
                <div class="detail-row">
                    <span>Date</span>
                    <span>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M j, Y') }}</span>
                </div>
            @endif
            <div class="detail-row" style="margin-top:8px; padding-top:8px; border-top:1px solid #f3f4f6;">
                <span>Total</span>
                <span>₱{{ number_format($booking->total_amount, 2) }}</span>
            </div>
        </div>

        {{-- CTA --}}
        <div class="cta-section">
            <a href="{{ url('/bookings/' . $booking->id . '/pay?group_id=' . $groupId) }}" class="cta-btn">
                Complete my booking now
            </a>
        </div>

        <p style="font-size:13px; color:#6b7280; line-height:1.6; text-align:center;">
            If you no longer wish to book, you can safely ignore this email.
        </p>
    </div>

    <div class="footer">
        <p>Cabanas Beach Resort · Tambobong, Dasol, Pangasinan</p>
        <p>This email was sent to {{ $booking->email ?? '' }} · <a href="{{ url('/') }}">Visit our website</a></p>
    </div>

</div>
</body>
</html>
