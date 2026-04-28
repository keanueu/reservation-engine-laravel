<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\BoatBooking;
use Carbon\Carbon;

class CleanupAbandonedBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cleanup-abandoned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete abandoned bookings that were never paid (older than 30 minutes)';

    public function handle()
    {
        $cutoff = Carbon::now()->subMinutes(30);
        
        try {
            // Delete room bookings that are waiting/pending and older than 30 minutes
            $deletedRooms = Booking::whereIn('status', ['waiting', 'pending'])
                ->where('payment_status', 'pending')
                ->where('created_at', '<', $cutoff)
                ->delete();

            // Delete boat bookings that are waiting/pending and older than 30 minutes
            $deletedBoats = BoatBooking::whereIn('status', ['waiting', 'pending'])
                ->where('payment_status', 'pending')
                ->where('created_at', '<', $cutoff)
                ->delete();

            $total = $deletedRooms + $deletedBoats;
            
            Log::info('Abandoned bookings cleanup completed', [
                'rooms_deleted' => $deletedRooms,
                'boats_deleted' => $deletedBoats,
                'total' => $total
            ]);

            $this->info("Cleanup completed: {$deletedRooms} room bookings and {$deletedBoats} boat bookings deleted.");
        } catch (\Throwable $e) {
            Log::error('CleanupAbandonedBookings failed', ['error' => $e->getMessage()]);
            $this->error('Cleanup failed: ' . $e->getMessage());
        }

        return 0;
    }
}
