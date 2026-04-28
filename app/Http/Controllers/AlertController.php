<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;
use App\Models\User;
use App\Notifications\TyphoonAlertNotification;
use App\Events\AlertCreated;
use Illuminate\Support\Facades\Notification;

class AlertController extends Controller
{
    // Admin: store a manual alert and notify users
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'severity' => 'required|string|max:50',
            'message' => 'required|string',
            'location' => 'nullable|string|max:255',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'send_email' => 'nullable|boolean',
        ]);

        $alert = Alert::create(array_merge($data, [
            'send_email' => !empty($data['send_email'])
        ]));

        // Notify users via Notification system (database + mail if requested).
        // We purposely do not broadcast events here; alerts are delivered via database notifications
        // so users can see them in-app (notifications dropdown).
        $users = User::whereNotNull('email')->get();
        Notification::send($users, new TyphoonAlertNotification($alert));

        return redirect()->back()->with('success', 'Alert created and notifications sent.');
    }

    // Optional GET route support - redirect to calamity admin page
    public function index()
    {
        return redirect()->route('admin.calamity.index');
    }

    // Public: return current active alert(s) as JSON
    public function current()
    {
        // return last few alerts or active by time window
        $alerts = Alert::orderBy('created_at', 'desc')->limit(10)->get();
        return response()->json(['data' => $alerts]);
    }

    // Authenticated user: return their unread notifications for in-app display
    public function userNotifications(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['data' => []]);
        $notifs = $user->unreadNotifications()->take(20)->get();
        return response()->json(['data' => $notifs]);
    }

    // Authenticated user: mark a specific notification as read
    public function markNotificationRead(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) return response()->json(['ok' => false], 401);
        $notif = $user->notifications()->where('id', $id)->first();
        if (!$notif) return response()->json(['ok' => false], 404);
        $notif->markAsRead();
        return response()->json(['ok' => true]);
    }
}
