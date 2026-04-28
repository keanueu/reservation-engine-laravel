<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReleaseBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:release';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark room bookings as completed when their checkout time has passed';

    public function handle()
    {
        $now = Carbon::now();
        try {
            $rows = DB::table('bookings')
                ->whereIn('status', ['paid', 'confirmed'])
                ->where(function($q) use ($now) {
                    $q->whereNotNull('actual_checkout_at')
                      ->where('actual_checkout_at', '<=', $now)
                      ->orWhere(function($qq) use ($now) {
                          $qq->whereNull('actual_checkout_at')
                             ->whereNotNull('scheduled_checkout_at')
                             ->where('scheduled_checkout_at', '<=', $now);
                      });
                })->get();

            foreach ($rows as $r) {
                DB::table('bookings')->where('id', $r->id)->update(['status' => 'completed', 'updated_at' => $now]);
                Log::info('Booking released (completed)', ['booking_id' => $r->id]);
            }

            $this->info('Booking release completed: ' . count($rows) . ' updated.');
        } catch (\Throwable $e) {
            Log::error('ReleaseBookings failed', ['error' => $e->getMessage()]);
            $this->error('ReleaseBookings failed: ' . $e->getMessage());
        }

        return 0;
    }
}
