<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingExtension;
use App\Services\PaymongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Carbon;
use App\Notifications\BookingReceiptNotification;

class BookingExtensionController extends Controller
{
    /**
     * User requests an extension (1,2,5 hours)
     */
    public function store(Request $request, Booking $booking)
    {
        $request->validate([
            'hours' => 'required|in:1,2,5',
            'payment_method' => 'required|in:online,frontdesk',
        ]);

        // Ensure booking exists and is currently active / checked-in
        // For minimal approach we won't gate-check the booking status; caller should ensure it.

        // Calculate price: derive hourly rate from nightly price (simple fallback)
        $room = $booking->room;
        $nightPrice = $room->price ?? 0;
        $hourly = $nightPrice / 24;
        $markup = config('booking.extension_markup', 1.25); // default 1.25 if not configured
        $hourly = round($hourly * $markup, 2);

        $hours = intval($request->input('hours'));
        $price = round($hourly * $hours, 2);

        $currentCheckout = Carbon::parse($booking->end_date);
        $newCheckout = $currentCheckout->copy()->addHours($hours);

        $extension = BookingExtension::create([
            'booking_id' => $booking->id,
            'hours' => $hours,
            'requested_by' => Auth::id(),
            'status' => $request->input('payment_method') === 'online' ? 'pending_payment' : 'pending_frontdesk',
            'price' => $price,
            'new_checkout_at' => $newCheckout,
        ]);

        // If online, return the extension record and price so client can redirect to payment flow
        $response = [
            'extension' => $extension,
            'message' => 'Extension created. Proceed to payment if online chosen.',
        ];

        if ($request->input('payment_method') === 'online') {
            // Provide a pay URL that redirects into the existing PaymentController flow.
            $response['pay_url'] = route('booking_extensions.pay', ['id' => $extension->id]);
        }

        return response()->json($response, 201);
    }

    /**
     * Admin approves and marks extension as paid (used by frontdesk when they collected cash)
     */
    public function approve(Request $request, $id)
    {
        $extension = BookingExtension::findOrFail($id);
        $booking = $extension->booking;

        // Basic conflict check: ensure no booking starts before new checkout
        $conflict = Booking::where('room_id', $booking->room_id)
            ->where('id', '!=', $booking->id)
            ->where('start_date', '<=', $extension->new_checkout_at->toDateString())
            ->where('start_date', '>=', $booking->end_date)
            ->exists();

        if ($conflict) {
            return response()->json(['error' => 'Extension conflicts with another booking.'], 422);
        }

        // mark as approved and paid (frontdesk will have collected cash)
        $extension->status = 'paid';
        $extension->processed_by = Auth::id();
        $extension->processed_at = Carbon::now();
        $extension->save();

        // Update booking end_date (store date portion to keep minimal DB changes)
        $booking->end_date = $extension->new_checkout_at->toDateString();
        $booking->paid_amount = ($booking->paid_amount ?? 0) + $extension->price;
        $booking->total_amount = ($booking->total_amount ?? 0) + $extension->price;
        $booking->save();

        // Send receipt email to guest for the extension
        try {
            Notification::route('mail', $booking->email)
                ->notify(new BookingReceiptNotification(collect([$booking]), 'extension', $extension));
        } catch (\Throwable $e) {
            // non-fatal: log and continue
            \Illuminate\Support\Facades\Log::error('Failed to send extension receipt email: ' . $e->getMessage());
        }
        return response()->json(['extension' => $extension, 'booking' => $booking]);
    }

    /**
     * Payment webhook/callback: mark extension paid when gateway notifies
     * Expects payload: extension_id, payment_id
     */
    public function webhook(Request $request)
    {
        // Accept either a direct extension_id or a payment_id (link id) from the gateway.
        $extension = null;

        if ($request->filled('extension_id')) {
            $extension = BookingExtension::find($request->input('extension_id'));
        }

        // If not found by extension_id, try to find by payment_id (e.g. PayMongo link id)
        if (!$extension && $request->filled('payment_id')) {
            $extension = BookingExtension::where('payment_id', $request->input('payment_id'))->first();
        }

        // If still not found, try to extract link id from common webhook shapes
        if (!$extension && $request->has('data')) {
            $data = $request->input('data');
            // If webhook contains attributes -> metadata -> extension_id
            $attrs = data_get($data, 'attributes', []);
            $meta = data_get($attrs, 'metadata', []);
            if (!empty($meta['extension_id'])) {
                $extension = BookingExtension::find($meta['extension_id']);
            }
            // Or try id of the resource (link id)
            if (!$extension && !empty($data['id'])) {
                $extension = BookingExtension::where('payment_id', $data['id'])->first();
            }
        }

        if (!$extension) {
            return response()->json(['error' => 'Extension not found'], 404);
        }

        // Determine payment id to store
        $paymentId = $request->input('payment_id') ?? data_get($request->input('data', []), 'id');
        if ($paymentId)
            $extension->payment_id = $paymentId;

        $extension->status = 'paid';
        $extension->processed_at = Carbon::now();
        $extension->save();

        $booking = $extension->booking;
        $booking->end_date = $extension->new_checkout_at->toDateString();
        $booking->paid_amount = ($booking->paid_amount ?? 0) + $extension->price;
        $booking->total_amount = ($booking->total_amount ?? 0) + $extension->price;
        $booking->save();

        // Send receipt email to guest for the extension
        try {
            Notification::route('mail', $booking->email)
                ->notify(new BookingReceiptNotification(collect([$booking]), 'extension', $extension));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send extension receipt email (webhook): ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }

    /**
     * Return pending online extensions for frontdesk to poll.
     */
    public function pendingExtensions(Request $request)
    {
        // Return extensions currently pending_payment so frontdesk can refresh indicators
        $pending = BookingExtension::where('status', 'pending_payment')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'booking_id', 'hours', 'price', 'payment_id', 'status', 'created_at']);

        return response()->json(['data' => $pending]);
    }

    /**
     * Refresh an extension's payment status by querying PayMongo link status.
     * Admin/frontdesk can call this if webhook isn't available or delayed.
     */
    public function refresh(Request $request, $id)
    {
        $extension = BookingExtension::findOrFail($id);

        if (empty($extension->payment_id)) {
            return response()->json(['error' => 'No payment_id on extension'], 422);
        }

        $paymongo = app(PaymongoService::class);
        try {
            $resp = $paymongo->getLink($extension->payment_id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to query PayMongo', 'detail' => $e->getMessage()], 500);
        }

        $attrs = data_get($resp, 'data.attributes', []);
        $paid = false;
        $paymentResourceId = null;

        // Some shapes may include a top-level status
        $topStatus = data_get($attrs, 'status');
        if (!empty($topStatus) && in_array(strtolower($topStatus), ['paid', 'succeeded', 'completed'])) {
            $paid = true;
        }

        // Or inspect payments array
        $payments = data_get($attrs, 'payments', []);
        foreach ($payments as $p) {
            $pstatus = data_get($p, 'attributes.status');
            if (!empty($pstatus) && in_array(strtolower($pstatus), ['paid', 'succeeded', 'completed'])) {
                $paid = true;
                $paymentResourceId = data_get($p, 'id');
                break;
            }
        }

        if (!$paid) {
            return response()->json(['paid' => false, 'extension' => $extension]);
        }

        // mark extension paid
        $extension->status = 'paid';
        if ($paymentResourceId)
            $extension->payment_id = $paymentResourceId;
        $extension->processed_at = Carbon::now();
        $extension->save();

        // update booking
        $booking = $extension->booking;
        $booking->end_date = $extension->new_checkout_at->toDateString();
        $booking->paid_amount = ($booking->paid_amount ?? 0) + $extension->price;
        $booking->total_amount = ($booking->total_amount ?? 0) + $extension->price;
        $booking->save();

        // Send receipt email to guest for the extension
        try {
            Notification::route('mail', $booking->email)
                ->notify(new BookingReceiptNotification(collect([$booking]), 'extension', $extension));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send extension receipt email (refresh): ' . $e->getMessage());
        }

        return response()->json(['paid' => true, 'extension' => $extension, 'booking' => $booking]);
    }

    /**
     * Return a single extension's current status and related data.
     */
    public function show($id)
    {
        $extension = BookingExtension::with('booking')->find($id);
        if (!$extension) {
            return response()->json(['error' => 'Extension not found'], 404);
        }

        return response()->json(['extension' => $extension]);
    }

    /**
     * Redirect user to the payment flow for this extension using existing PaymentController
     */
    public function pay($id)
    {
        $extension = BookingExtension::findOrFail($id);
        $booking = $extension->booking;

        $amountPhp = (float) $extension->price;
        $amount = max(10000, (int) round($amountPhp * 100)); // centavos

        // Build metadata so webhook or link can be mapped back to this extension
        $metadata = [
            'type' => 'extension',
            'extension_id' => $extension->id,
            'booking_id' => $booking->id,
            'customer_name' => $booking->name,
            'customer_email' => $booking->email,
            'description' => "Extension #{$extension->id} for Booking {$booking->id} ({$extension->hours}h)",
        ];

        // Create PayMongo link directly and save payment_id to extension
        $paymongo = app(PaymongoService::class);
        $resp = $paymongo->createLink($amount, 'PHP', $metadata);

        // createLink returns a wrapper with 'success' and 'raw' keys.
        // Prefer returned direct values but fall back to raw API shape for compatibility.
        $linkId = $resp['link_id'] ?? data_get($resp, 'raw.data.id');
        $checkoutUrl = $resp['checkout_url'] ?? data_get($resp, 'raw.data.attributes.checkout_url') ?? data_get($resp, 'raw.data.attributes.url');

        if ($linkId) {
            $extension->payment_id = $linkId;
            $extension->save();
        }

        if ($checkoutUrl) {
            return redirect()->away($checkoutUrl);
        }

        // If the service returned an error, stringify it safely and log for debugging
        $rawErr = $resp['message'] ?? ($resp['raw'] ?? null);
        if (is_array($rawErr) || is_object($rawErr)) {
            $errMsg = json_encode($rawErr);
        } elseif (!empty($rawErr) || $rawErr === '0') {
            $errMsg = (string) $rawErr;
        } else {
            $errMsg = null;
        }

        // Log the failure for easier debugging
        \Illuminate\Support\Facades\Log::error('PayMongo createLink failed for extension', ['extension_id' => $extension->id, 'resp' => $resp]);

        return redirect()->back()->with('error', 'Unable to create payment link for extension.' . ($errMsg ? ' ' . $errMsg : ''));
    }
}
