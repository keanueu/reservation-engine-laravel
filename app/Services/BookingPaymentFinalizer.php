<?php

namespace App\Services;

use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use App\Models\BoatBooking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class BookingPaymentFinalizer
{
    public function markPaid(
        ?string $groupId,
        ?string $sessionId = null,
        ?string $paymentId = null,
        float $amountPaid = 0.0
    ): array {
        $result = [
            'group_id' => $groupId,
            'rooms_updated' => 0,
            'boats_updated' => 0,
            'newly_paid_count' => 0,
            'amount_php' => $amountPaid,
        ];

        if (!$groupId) {
            $groupId = $this->resolveGroupId($sessionId, $paymentId);
            $result['group_id'] = $groupId;
        }

        if (!$groupId && !$sessionId && !$paymentId) {
            Log::error('[PaymentFinalizer] No identifiers available to mark payment as paid');
            return $result;
        }

        DB::transaction(function () use ($groupId, $sessionId, $paymentId, $amountPaid, &$result) {
            $roomQuery = Booking::query();
            $boatQuery = BoatBooking::query();

            $this->applyIdentifier($roomQuery, $boatQuery, $groupId, $sessionId, $paymentId);

            $unpaidRooms = (clone $roomQuery)
                ->where(fn ($query) => $query->where('payment_status', '!=', 'paid')->orWhereNull('payment_status'))
                ->count();
            $unpaidBoats = (clone $boatQuery)
                ->where(fn ($query) => $query->where('payment_status', '!=', 'paid')->orWhereNull('payment_status'))
                ->count();

            $common = [
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'paid_at' => now(),
            ];

            if ($paymentId) {
                $common['payment_id'] = $paymentId;
            } elseif ($sessionId) {
                $common['payment_id'] = $sessionId;
            }

            $roomUpdate = $common + [
                'paid_amount' => $this->paidAmountValue('bookings', $amountPaid),
            ];
            $boatUpdate = $common + [
                'paid_amount' => $this->paidAmountValue('boat_bookings', $amountPaid),
            ];

            if (Schema::hasColumn('bookings', 'expires_at')) {
                $roomUpdate['expires_at'] = null;
            }

            $result['rooms_updated'] = (clone $roomQuery)->update($roomUpdate);
            $result['boats_updated'] = (clone $boatQuery)->update($boatUpdate);
            $result['newly_paid_count'] = $unpaidRooms + $unpaidBoats;
        });

        Log::info('[PaymentFinalizer] Payment marked paid', $result);

        return $result;
    }

    public function markFailed(
        ?string $groupId,
        ?string $sessionId = null,
        ?string $paymentId = null
    ): array {
        $result = [
            'group_id' => $groupId,
            'rooms_updated' => 0,
            'boats_updated' => 0,
        ];

        if (!$groupId) {
            $groupId = $this->resolveGroupId($sessionId, $paymentId);
            $result['group_id'] = $groupId;
        }

        if (!$groupId && !$sessionId && !$paymentId) {
            Log::warning('[PaymentFinalizer] No identifiers available to mark payment as failed');
            return $result;
        }

        $roomQuery = Booking::query();
        $boatQuery = BoatBooking::query();

        $this->applyIdentifier($roomQuery, $boatQuery, $groupId, $sessionId, $paymentId);

        $notPaid = fn ($query) => $query->where('payment_status', '!=', 'paid')->orWhereNull('payment_status');

        $result['rooms_updated'] = $roomQuery->where($notPaid)->update(['payment_status' => 'failed']);
        $result['boats_updated'] = $boatQuery->where($notPaid)->update(['payment_status' => 'failed']);

        Log::info('[PaymentFinalizer] Payment marked failed for unpaid bookings only', $result);

        return $result;
    }

    public function sendConfirmationMail(string $groupId): void
    {
        try {
            $roomBookings = Booking::with('room')
                ->where('group_id', $groupId)
                ->where('payment_status', 'paid')
                ->get();

            $boatBookings = BoatBooking::with('boat')
                ->where('group_id', $groupId)
                ->where('payment_status', 'paid')
                ->get();

            $allBookings = $roomBookings->concat($boatBookings);
            $first = $allBookings->first();

            if (!$first) {
                Log::warning('[PaymentFinalizer][Mail] No paid bookings found for group', ['group_id' => $groupId]);
                return;
            }

            if (empty($first->email)) {
                Log::warning('[PaymentFinalizer][Mail] Booking has no email address', [
                    'group_id' => $groupId,
                    'booking_id' => $first->id,
                ]);
                return;
            }

            $totalPaid = (float) ($allBookings->sum('paid_amount') ?: $allBookings->sum('deposit_amount'));

            Mail::to($first->email)->queue(new BookingConfirmedMail(
                bookings: $allBookings->all(),
                totalPaid: $totalPaid,
                groupId: $groupId,
            ));

            Log::info('[PaymentFinalizer][Mail] Booking confirmation queued', [
                'group_id' => $groupId,
                'email' => $first->email,
                'total_paid' => $totalPaid,
                'booking_count' => $allBookings->count(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[PaymentFinalizer][Mail] Failed to send booking confirmation', [
                'group_id' => $groupId,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function resolveGroupId(?string $sessionId, ?string $paymentId): ?string
    {
        foreach (array_filter([$sessionId, $paymentId]) as $identifier) {
            $booking = Booking::where('payment_id', $identifier)->first()
                ?? BoatBooking::where('payment_id', $identifier)->first();

            if ($booking?->group_id) {
                return $booking->group_id;
            }
        }

        return null;
    }

    private function applyIdentifier(
        Builder $roomQuery,
        Builder $boatQuery,
        ?string $groupId,
        ?string $sessionId,
        ?string $paymentId
    ): void {
        if ($groupId) {
            $roomQuery->where('group_id', $groupId);
            $boatQuery->where('group_id', $groupId);

            return;
        }

        if ($sessionId) {
            $roomQuery->where('payment_id', $sessionId);
            $boatQuery->where('payment_id', $sessionId);

            return;
        }

        $roomQuery->where('payment_id', $paymentId);
        $boatQuery->where('payment_id', $paymentId);
    }

    private function paidAmountValue(string $table, float $amountPaid): float|\Illuminate\Database\Query\Expression
    {
        if ($amountPaid > 0) {
            return $amountPaid;
        }

        $columns = collect(['total_to_charge', 'deposit_amount', 'total_amount'])
            ->filter(fn (string $column) => Schema::hasColumn($table, $column))
            ->all();

        if (empty($columns)) {
            return DB::raw('0');
        }

        return DB::raw('COALESCE(' . implode(', ', $columns) . ')');
    }
}
