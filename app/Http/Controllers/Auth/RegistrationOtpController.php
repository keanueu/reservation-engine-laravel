<?php
// app/Http/Controllers/Auth/RegistrationOtpController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\RegistrationOtpService;
use Illuminate\Support\Facades\Auth;

class RegistrationOtpController extends Controller
{
    public function form()
    {
        if (! session('registration_otp_user_id')) {
            return redirect()->route('register');
        }

        return view('auth.registration-otp');
    }

   public function verify(Request $request)
{
    $request->validate(['code' => 'required|digits:6']);

    $user = User::find(session('registration_otp_user_id'));
    if (! $user) return redirect()->route('register');

    if (RegistrationOtpService::verify($user, $request->code)) {
        // Mark the user as verified
        $user->update(['is_verified' => true]);
        session()->forget('registration_otp_user_id');

        // Do NOT log in automatically
        return redirect()->route('login')->with('status', 'Your account has been verified! Please log in.');
    }

    return back()->withErrors(['code' => 'Invalid or expired OTP.']);
}


    public function resend()
    {
        $user = User::find(session('registration_otp_user_id'));
        if (! $user) return redirect()->route('register');

        RegistrationOtpService::generate($user);

        return back()->with('status', 'A new OTP has been sent.');
    }
}
