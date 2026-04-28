<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boat;
use App\Models\BoatBooking;
use Illuminate\Support\Facades\Auth;

class BoatBookingController extends Controller
{
    public function add_boat_booking(Request $request, $id)
    {
        $boat = Boat::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'You must be logged in to book a boat.');
        }

        $booking_date = $request->input('booking_date', now()->toDateString());

        // Optional limits
        $boatsBookedCount = BoatBooking::where('booking_date', $booking_date)->count();
        if ($boatsBookedCount >= 2) {
            return redirect()->back()->with('error', 'Maximum boats are fully booked for this day.');
        }

        $groupId = $request->input('group_id') ?? uniqid('grp_', true);

        // Reuse existing payment_id if present
        $paymentId = $request->input('payment_id') ?? null;

        $booking = new BoatBooking();
        $booking->group_id = $groupId;
        $booking->boat_id = $boat->id;
        $booking->user_id = $user->id;
        $booking->name = $user->name;
        $booking->email = $user->email;
        $booking->phone = $user->phone;
        $booking->booking_date = $booking_date;
        $booking->day = 1;
        $booking->start_time = $request->input('start_time', now()->format('H:i:s'));
        $booking->end_time = $request->input('end_time', now()->addHour()->format('H:i:s'));
        $booking->guests = $request->input('guests', 1);
        $booking->total_amount = $boat->price;
        $booking->status = 'waiting';
        $booking->payment_status = 'pending';
        $booking->payment_id = $paymentId;
        $booking->save();

        return redirect()->back()->with('success', 'Boat booked successfully!');
    }


    public function delete_boat_booking($id)
    {
        $booking = BoatBooking::findOrFail($id);
        $booking->delete();
        return redirect()->back()->with('success', 'Boat booking deleted successfully.');
    }

    public function approve_boat_booking($id)
    {
        $booking = BoatBooking::findOrFail($id);
        $booking->status = 'approve';
        $booking->save();
        return redirect()->back()->with('success', 'Boat booking approved.');
    }

    public function reject_boat_booking($id)
    {
        $booking = BoatBooking::findOrFail($id);
        $booking->status = 'rejected';
        $booking->save();
        return redirect()->back()->with('success', 'Boat booking rejected.');
    }

    public function showSendBoatBookingEmail($id)
    {
        $booking = BoatBooking::findOrFail($id);
        // return view for sending boat booking email (update to your new view if needed)
        $booking = BoatBooking::findOrFail($id);
        return view('frontdesk.send_boat_booking_email', compact('booking'));
    }

    public function sendBoatBookingEmail(Request $request, $id)
    {
        $booking = BoatBooking::findOrFail($id);
        // Here you would implement the actual email sending logic
        // Example:
        // Mail::to($booking->email)->send(new BoatBookingMail($booking, $request->all()));
        return redirect()->back()->with('success', 'Boat booking email sent successfully.');
    }
}
