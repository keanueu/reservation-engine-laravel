<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Alert;
use App\Notifications\TyphoonAlertNotification;
use Illuminate\Support\Facades\Notification;
use App\Events\AlertCreated;

class GenerateAlertsFromFeed extends Command
{
    protected $signature = 'alerts:fetch';
    protected $description = 'Fetch typhoon feed and create alerts when severity changes';

    public function handle()
    {
        // endpoint assumed
        $endpoint = url('/check-typhoon-status');
        try {
            $res = Http::get($endpoint);
            if (!$res->ok()) {
                $this->error('Failed to fetch feed');
                return 1;
            }
            $data = $res->json();
            if (!$data) {
                $this->info('No data');
                return 0;
            }

            // map severity and message
            $severity = $data['status'] ?? ($data['severity'] ?? 'clear');
            $location = $data['location'] ?? ($data['area'] ?? null);
            $message = $data['message'] ?? ($data['description'] ?? null);

            // find last alert with same location
            $last = Alert::where('location', $location)->orderBy('created_at', 'desc')->first();
            $changed = false;
            if (!$last) {
                $changed = true;
            } else {
                $prev = strtolower($last->severity ?? '');
                if ($prev !== strtolower($severity)) $changed = true;
            }

            if ($changed) {
                $alert = Alert::create([
                    'title' => $data['title'] ?? null,
                    'severity' => $severity,
                    'message' => $message ?? ('Update: ' . ($data['title'] ?? 'Weather update')),
                    'location' => $location,
                    'starts_at' => now(),
                    'ends_at' => null,
                    'send_email' => false,
                    'meta' => $data,
                ]);

                // broadcast
                event(new AlertCreated($alert));

                // notify users
                $users = \App\Models\User::whereNotNull('email')->get();
                Notification::send($users, new TyphoonAlertNotification($alert));

                $this->info('Created alert: ' . ($alert->id ?? 'n/a'));
            } else {
                $this->info('No change in severity');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            return 1;
        }
    }
}
