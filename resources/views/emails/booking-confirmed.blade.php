<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
    <style>
        body { margin: 0; padding: 0; background: #f5f5f5; font-family: 'Manrope', Arial, sans-serif; color: #111827; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border: 1px solid #e5e7eb; }
        .header { background: #964B00; padding: 32px 40px; text-align: center; }
        .header h1 { margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.05em; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,0.8); font-size: 13px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; margin-bottom: 20px; }
        .status-badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; margin-bottom: 24px; }
        .section-label { font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #6b7280; margin-bottom: 10px; }
        .booking-card { border: 1px solid #e5e7eb; padding: 16px 20px; margin-bottom: 12px; }
        .booking-card .room-name { font-size: 15px; font-weight: 600; color: #111827; margin-bottom: 8px; }
        .booking-card .detail-row { display: flex; justify-content: space-between; font-size: 13px; color: #4b5563; margin-bottom: 4px; }
        .booking-card .detail-row span:last-child { font-weight: 600; color: #111827; }
        .total-row { border-top: 2px solid #111827; padding-top: 14px; margin-top: 20px; display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; }
        .deposit-note { font-size: 12px; color: #6b7280; margin-top: 6px; }
        .cta-section { text-align: center; margin: 32px 0; }
        .cta-btn { display: inline-block; background: #964B00; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 14px 32px; }
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

    <div class="body">
        @php
            $first = collect($bookings)->first();
            $guestName = $first->name ?? 'Guest';
        @endphp

        <div class="greeting">Hello, <strong>{{ $guestName }}</strong> 👋</div>

        <div class="status-badge">✓ Booking confirmed</div>

        <p style="font-size:14px; color:#4b5563; margin-bottom:28px; line-height:1.6;">
            Your deposit payment has been received and your booking is now confirmed.
            We look forward to welcoming you to Cabanas Beach Resort!
        </p>

        {{-- Booking items --}}
        <div class="section-label">Your reservations</div>

        @foreach($bookings as $booking)
            <div class="booking-card">
                @if(isset($booking->room_id))
                    <div class="room-name">
                        {{ optional($booking->room)->room_name ?? 'Room Booking #' . $booking->id }}
                    </div>
                    <div class="detail-row">
                        <span>Check-in</span>
                        <span>{{ \Carbon\Carbon::parse($booking->start_date)->format('M j, Y') }}
                            @if($booking->scheduled_checkin_at)
                                at {{ \Carbon\Carbon::parse($booking->scheduled_checkin_at)->format('g:i A') }}
                            @endif
                        </span>
                    </div>
                    <div class="detail-row">
                        <span>Check-out</span>
                        <span>{{ \Carbon\Carbon::parse($booking->end_date)->format('M j, Y') }}
                            @if($booking->scheduled_checkout_at)
                                at {{ \Carbon\Carbon::parse($booking->scheduled_checkout_at)->format('g:i A') }}
                            @endif
                        </span>
                    </div>
                    <div class="detail-row">
                        <span>Guests</span>
                        <span>{{ $booking->adults }} adult{{ $booking->adults != 1 ? 's' : '' }}
                            @if($booking->children > 0), {{ $booking->children }} child{{ $booking->children != 1 ? 'ren' : '' }}@endif
                        </span>
                    </div>
                @else
                    <div class="room-name">
                        {{ optional($booking->boat)->name ?? 'Boat Booking #' . $booking->id }}
                    </div>
                    <div class="detail-row">
                        <span>Date</span>
                        <span>{{ \Carbon\Carbon::parse($booking->booking_date)->format('M j, Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Time</span>
                        <span>{{ $booking->start_time }} – {{ $booking->end_time }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Guests</span>
                        <span>{{ $booking->guests }}</span>
                    </div>
                @endif
                <div class="detail-row" style="margin-top:8px; padding-top:8px; border-top:1px solid #f3f4f6;">
                    <span>Room total</span>
                    <span>₱{{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
        @endforeach

        {{-- Payment summary --}}
        <div class="total-row">
            <span>Deposit paid</span>
            <span>₱{{ number_format($totalPaid, 2) }}</span>
        </div>
        <p class="deposit-note">Remaining balance is due at check-in.</p>

        {{-- CTA --}}
        <div class="cta-section">
            <a href="{{ url('/my-bookings') }}" class="cta-btn">View my bookings</a>
        </div>

        <p style="font-size:13px; color:#6b7280; line-height:1.6;">
            If you have any questions, please contact us at the front desk or reply to this email.
            We're happy to help!
        </p>
    </div>

    <div class="footer">
        <p>Cabanas Beach Resort · Tambobong, Dasol, Pangasinan</p>
        <p>This email was sent to {{ $first->email ?? '' }} · <a href="{{ url('/') }}">Visit our website</a></p>
    </div>

</div>
</body>
</html>
