<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Boat;
use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Support\Str;
class HomeController extends Controller
{
    // Boat details page
    public function boat_details($id)
    {
        $boat = Boat::findOrFail($id);
        return view('home.boat_details', compact('boat'));
    }

    public function room_details($id)
    {
        $room = Room::findOrFail($id); // use findOrFail to avoid null
        return view('home.room_details', compact('room'));
    }

    public function room_detailsv2($id)
    {
        $room = Room::with('images')->findOrFail($id);

        // Check promo exists
        if (!$room->promo_price) {
            abort(404);
        }

        return view('home.room_detailsv2', compact('room'));
    }


    //confirmation at validation
    public function add_booking(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
        ]);

        $room = Room::find($id);
        if (!$room) {
            return redirect()->back()->with('error', 'Room not found.');
        }

        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $nights = (new \DateTime($endDate))->diff(new \DateTime($startDate))->days;
        if ($nights < 1)
            $nights = 1;
        // Compute unit price taking any active discount/promo into account
        $discount = $room->discounts->first() ?? null;
        $discountValue = optional($discount)->amount ?? 0;
        $isPercentage = optional($discount)->amount_type === 'percent' || optional($discount)->amount_type === 'percentage';
        $isFixedAmount = optional($discount)->amount_type === 'fixed';
        $isActive = optional($discount)->active ?? false;

        $unitPrice = $room->price;
        if ($isActive && $discountValue > 0) {
            if ($isPercentage) {
                $unitPrice = $room->price * (1 - ($discountValue / 100));
            } elseif ($isFixedAmount) {
                $unitPrice = max(0, $room->price - $discountValue);
            }
        }

        $totalAmount = $unitPrice * $nights;

        $isBooked = Booking::where('room_id', $id)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)->exists();
        if ($isBooked) {
            return redirect()->back()->with('message', 'This room is already booked for the selected dates.');
        }

        $groupId = Str::uuid()->toString();
        $depositPercent = (float) Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $depositFeePercent = (float) Setting::get('deposit_fee_percentage', config('booking.deposit_fee_percentage', 0));
        $depositAmount = round($totalAmount * ($depositPercent / 100), 2);
        $depositFee = round($depositAmount * ($depositFeePercent / 100), 2);
        $totalToCharge = round($depositAmount + $depositFee, 2);

        $booking = new Booking();
        $booking->group_id = $groupId;
        $booking->room_id = $id;
        $booking->name = $request->name;
        $booking->email = $request->email;
        $booking->phone = $request->phone;
        $booking->adults = $request->adults;
        $booking->children = $request->children;
        $booking->start_date = $startDate;
        $booking->end_date = $endDate;
        $booking->nights = $nights;
        $booking->total_amount = $totalAmount;
        $booking->status = 'waiting';
        $booking->payment_status = 'pending';
        $booking->deposit_amount = $depositAmount;
        $booking->deposit_fee = $depositFee;
        $booking->total_to_charge = $totalToCharge;
        $booking->save();

        session([
            'pending_booking_group' => $groupId,
            'pending_booking_ids' => [$booking->id],
        ]);

        return redirect()->route('bookings.pay', [
            'booking' => $booking->id,
            'group_id' => $groupId,
            'amount' => $totalToCharge,
        ]);
    }


    public function contact(Request $request)
    {
        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->message = $request->message;

        $contact->save();
        return redirect()->back()->with('message', 'Message Sent Successfully');
    }



    //confirmation at validation
    public function add_boat_booking(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after:startDate',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
        ]);

        $data = new Booking;
        $room = Room::find($id);

        $data->room_id = $room->id;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->adults = $request->adults;
        $data->children = $request->children;

        $startDate = $request->startDate;
        $endDate = $request->endDate;


        $isBooked = Booking::where('room_id', $id)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)->exists();

        if ($isBooked) {
            return redirect()->back()->with('message', 'This room is already booked for the selected dates.');
        }

        $booking = new Booking();
        $booking->room_id = $id;
        $booking->name = $request->name;
        $booking->email = $request->email;
        $booking->phone = $request->phone;
        $booking->adults = $request->adults;
        $booking->children = $request->children;
        $booking->start_date = $request->startDate;
        $booking->end_date = $request->endDate;
        $booking->status = 'waiting';
        $booking->save();

        return redirect()->back()->with('message', 'Booking request sent successfully!');
    }


}

