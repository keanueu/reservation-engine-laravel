<?php

namespace App\Services;

use App\Models\Booking;
use App\Services\PaymongoService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\DTOs\RefundRequestDTO;
use App\Models\Setting;

class RefundService
{
    /**
     * Submit a refund request.
     * Throws \RuntimeException on validation or rule failure.
     */
    public function requestRefund(Booking $booking, RefundRequestDTO $payload, ?string $requestingEmail, bool $isStaff = false): void
    {
        // Enforce user ownership if not staff
        if (!$isStaff && $requestingEmail && $booking->email !== $requestingEmail) {
            throw new \RuntimeException('You are not allowed to request a refund for this booking.');
        }

        // Enforce one refund per booking group
        if (!empty($booking->group_id)) {
            $groupBookings = Booking::where('group_id', $booking->group_id)
                ->select('id', 'group_id', 'refund_status')
                ->get();
            $groupHasRefund = $groupBookings->contains(function ($item) {
                return in_array(strtolower($item->refund_status ?? ''), ['requested', 'processing', 'refunded']);
            });
            if ($groupHasRefund) {
                throw new \RuntimeException('A refund has already been requested for this booking group. Only one refund request is allowed.');
            }
        }

        // Only allow refund request if booking was paid
        if (($booking->payment_status ?? '') !== 'paid') {
            throw new \RuntimeException('Refund can only be requested for paid bookings.');
        }

        $requested = $payload->amount ?? (float) ($booking->paid_amount ?? $booking->total_amount ?? 0);

        // Determine maximum refundable amount
        $depositPercent = (float) config('booking.deposit_percentage', 50) / 100;
        $groupDepositTotal = 0;
        if (!empty($booking->group_id)) {
            $groupBookings = Booking::where('group_id', $booking->group_id)
                ->select('id', 'group_id', 'deposit_amount', 'total_amount')
                ->get();
            foreach ($groupBookings as $gb) {
                $bDeposit = $gb->deposit_amount ?? round(($gb->total_amount ?? 0) * $depositPercent, 2);
                $groupDepositTotal += (float) $bDeposit;
            }
        } else {
            $groupDepositTotal = (float) ($booking->deposit_amount ?? ($booking->total_amount * $depositPercent));
        }

        if ($requested > $groupDepositTotal) {
            throw new \RuntimeException('Requested refund cannot exceed total deposited amount for your rooms (₱' . number_format($groupDepositTotal, 2) . ').');
        }

        $feePercent = (float) Setting::get('refund_fee_percentage', 5) / 100;
        $fee = round($requested * $feePercent, 2);
        $net = round(max(0, $requested - $fee), 2);

        $booking->refund_status = 'requested';
        $booking->refund_requested_amount = $requested;
        $booking->refund_fee = $fee;
        $booking->refund_amount = $net;
        $booking->refund_reason = $payload->reason;
        $booking->status = 'cancelled';
        $booking->save();
    }

    /**
     * Admin approval of refund, executing payment gateway logic if needed.
     * Throws \RuntimeException on gateway failure.
     */
    public function approveRefund(Booking $booking): void
    {
        $amount = $booking->refund_amount ?? $booking->paid_amount ?? $booking->total_amount ?? 0;
        $booking->refund_amount = $amount;

        $useGateway = config('booking.refund_via_gateway', false);

        if ($useGateway && !empty($booking->payment_id)) {
            $booking->refund_status = 'processing';
            $booking->save();

            $service = new PaymongoService();
            $amountCents = (int) round($amount * 100);
            $result = $service->refundPayment($booking->payment_id, $amountCents, $booking->refund_reason ?? null);

            if (!empty($result['success'])) {
                if (Schema::hasColumn('bookings', 'paymongo_refund_id')) {
                    $booking->paymongo_refund_id = $result['refund_id'] ?? null;
                }
                $booking->refund_status = 'refunded';
                $booking->status = 'cancelled';
                $booking->refunded_at = now();
                $booking->save();
                return;
            }

            Log::error('PayMongo refund failed', ['booking_id' => $booking->id, 'response' => $result]);
            $booking->refund_status = 'refund_failed';
            $booking->save();
            throw new \RuntimeException('PayMongo refund failed: ' . (is_array($result['message'] ?? null) ? json_encode($result['message']) : ($result['message'] ?? 'Unknown error')));
        }

        $booking->refund_status = 'refunded';
        $booking->status = 'cancelled';
        $booking->refunded_at = now();
        $booking->save();
    }

    /**
     * Reject a refund request.
     */
    public function rejectRefund(Booking $booking): void
    {
        $booking->refund_status = 'rejected';
        $booking->save();
    }
}
