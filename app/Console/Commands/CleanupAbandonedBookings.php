<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\Models\BoatBooking;
use App\Mail\BookingHoldExpiringMail;

class CleanupAbandonedBookings extends Command
{
    protected $signature = 'bookings:cleanup-abandoned
                            {--dry-run : Show what would be deleted without deleting}
                            {--skip-warnings : Skip sending hold-expiring warning emails}';

    protected $description = 'Release expired pending/waiting bookings and warn guests whose hold is about to expire';

    // How many minutes before expiry to send the warning email
    const WARNING_BEFORE_MINUTES = 5;

    public function handle(): int
    {
        $dryRun       = $this->option('dry-run');
        $skipWarnings = $this->option('skip-warnings');

        // -------------------------------------------------------
        // PHASE 1: Send "hold expiring soon" warnings
        // -------------------------------------------------------
        if (!$skipWarnings) {
            $this->sendExpiryWarnings($dryRun);
        }

        // -------------------------------------------------------
        // PHASE 2: Delete expired bookings (atomic)
        // -------------------------------------------------------
        try {
            [$rooms, $boats] = DB::transaction(function () use ($dryRun) {

                // Room bookings: expired by expires_at OR by created_at fallback
                $roomQuery = Booking::where('status', 'waiting')
                    ->where('payment_status', 'pending')
                    ->where(function ($q) {
                        $q->where(function ($q2) {
                            $q2->whereNotNull('expires_at')
                               ->where('expires_at', '<=', now());
                        })->orWhere(function ($q2) {
                            $q2->whereNull('expires_at')
                               ->where('created_at', '<=', now()->subMinutes(Booking::PENDING_TTL_MINUTES));
                        });
                    });

                // Boat bookings: use created_at fallback (no expires_at column)
                $boatQuery = BoatBooking::where('status', 'waiting')
                    ->where('payment_status', 'pending')
                    ->where('created_at', '<=', now()->subMinutes(Booking::PENDING_TTL_MINUTES));

                if ($dryRun) {
                    return [$roomQuery->count(), $boatQuery->count()];
                }

                return [$roomQuery->delete(), $boatQuery->delete()];
            });

            $label = $dryRun ? '[DRY RUN]' : 'Deleted';
            $this->info("{$label} {$rooms} expired room booking(s) and {$boats} expired boat booking(s).");

            if (!$dryRun && ($rooms + $boats) > 0) {
                Log::info('bookings:cleanup-abandoned', [
                    'rooms_released' => $rooms,
                    'boats_released' => $boats,
                ]);
            }

        } catch (\Throwable $e) {
            Log::error('bookings:cleanup-abandoned failed', ['error' => $e->getMessage()]);
            $this->error('Cleanup failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    // -------------------------------------------------------
    // Send warning emails to guests whose hold expires soon
    // -------------------------------------------------------
    private function sendExpiryWarnings(bool $dryRun): void
    {
        // Find bookings expiring in the next WARNING_BEFORE_MINUTES minutes
        // that have NOT already been warned (warned_at is null)
        $warningWindow = now()->addMinutes(self::WARNING_BEFORE_MINUTES);

        $expiringSoon = Booking::where('status', 'waiting')
            ->where('payment_status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())           // not yet expired
            ->where('expires_at', '<=', $warningWindow) // but expiring within window
            ->whereNull('warned_at')                    // not already warned
            ->whereNotNull('email')
            ->get();

        if ($expiringSoon->isEmpty()) {
            return;
        }

        $warned = 0;

        foreach ($expiringSoon as $booking) {
            $minutesLeft = (int) ceil(now()->diffInMinutes($booking->expires_at, false));
            if ($minutesLeft <= 0) continue;

            if ($dryRun) {
                $this->line("[DRY RUN] Would warn {$booking->email} — expires in {$minutesLeft} min (booking #{$booking->id})");
                $warned++;
                continue;
            }

            try {
                Mail::to($booking->email)
                    ->send(new BookingHoldExpiringMail(
                        booking:     $booking,
                        minutesLeft: $minutesLeft,
                        groupId:     $booking->group_id ?? '',
                    ));

                // Mark as warned so we don't send again on the next minute run
                $booking->update(['warned_at' => now()]);
                $warned++;

                Log::info('bookings:cleanup-abandoned — hold-expiring warning sent', [
                    'booking_id'  => $booking->id,
                    'email'       => $booking->email,
                    'minutes_left'=> $minutesLeft,
                ]);
            } catch (\Throwable $e) {
                Log::error('bookings:cleanup-abandoned — warning email failed', [
                    'booking_id' => $booking->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        if ($warned > 0) {
            $label = $dryRun ? '[DRY RUN]' : 'Sent';
            $this->info("{$label} {$warned} hold-expiring warning email(s).");
        }
    }
}
