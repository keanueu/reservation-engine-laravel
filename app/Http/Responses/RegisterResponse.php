<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Illuminate\Http\Request;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // Registration complete, ensure user is not authenticated (Fortify may auto-login)
        // Keep the `registration_otp_user_id` in session so the OTP form can access it.
        if (session('registration_otp_user_id')) {
            // Log out any authenticated user to enforce manual login after verification
            if (auth()->check()) {
                auth()->logout();
                // Don't invalidate the whole session because it contains the OTP user id.
                // Regenerate CSRF token for safety.
                $request->session()->regenerateToken();
            }
        }

        // Redirect to OTP verification form with status message
        return redirect()->route('registration.otp.form')->with('status', 'A verification code has been sent to your email. Please enter it below.');
    }
}
