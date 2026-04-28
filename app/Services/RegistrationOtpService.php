<?php
// app/Services/RegistrationOtpService.php
namespace App\Services;

use App\Models\User;
use App\Models\RegistrationOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RegistrationOtpNotification;

class RegistrationOtpService
{
    public static function generate(User $user)
    {
        $otp = random_int(100000, 999999); // 6-digit numeric OTP

        // Delete any previous OTPs for this user
        RegistrationOtp::where('user_id', $user->id)->delete();

        RegistrationOtp::create([
            'user_id' => $user->id,
            'otp_code' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via email
        $user->notify(new RegistrationOtpNotification($otp));

        return $otp;
    }

    public static function verify(User $user, $code)
    {
        $otp = RegistrationOtp::where('user_id', $user->id)->latest()->first();

        \Log::info('OTP verification attempt', [
            'user_id' => $user->id,
            'input_code' => $code,
            'db_otp' => $otp ? $otp->otp_code : null,
            'expired' => $otp ? $otp->isExpired() : null,
            'expires_at' => $otp ? $otp->expires_at : null,
            'otp_exists' => $otp ? true : false,
        ]);

        if (! $otp || $otp->isExpired()) return false;

        if (Hash::check($code, $otp->otp_code)) {
            $otp->delete(); // OTP is one-time use
            return true;
        }

        return false;
    }
}
