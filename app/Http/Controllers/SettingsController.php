<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    // Display settings form (frontdesk)
    public function index()
    {
        $deposit = Setting::get('deposit_percentage', config('booking.deposit_percentage', 50));
        $refundFee = Setting::get('refund_fee_percentage', 5);
        $includeRefundFeeInForm = Setting::get('include_refund_fee_in_form', 0);

        return view('frontdesk.settings', compact('deposit', 'refundFee', 'includeRefundFeeInForm'));
    }

    // Update settings
    public function update(Request $request)
    {
        $data = $request->validate([
            'deposit_percentage' => 'required|in:30,40,50',
            'refund_fee_percentage' => 'required|in:0,5,10,15',
            'include_refund_fee_in_form' => 'nullable|in:0,1',
        ]);

        Setting::set('deposit_percentage', $data['deposit_percentage']);
        Setting::set('refund_fee_percentage', $data['refund_fee_percentage']);
        Setting::set('include_refund_fee_in_form', $request->has('include_refund_fee_in_form') ? '1' : '0');

        return redirect()->back()->with('success', 'Settings updated.');
    }
}
