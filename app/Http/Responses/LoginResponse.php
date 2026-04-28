<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        $redirect = '/home';
        if ($user && isset($user->usertype)) {
            switch ($user->usertype) {
                case 'admin':
                    $redirect = '/admin/dashboard';
                    break;
                case 'frontdesk':
                    $redirect = '/frontdesk/home';
                    break;
                default:
                    $redirect = '/home';
            }
        }
        \Log::info('LoginResponse: usertype=' . ($user->usertype ?? 'none') . ', email=' . ($user->email ?? 'none') . ', redirect=' . $redirect);
        // Use a forced redirect instead of intended() to avoid prior intended URL overriding role-based redirect
        return redirect($redirect);
    }
}
