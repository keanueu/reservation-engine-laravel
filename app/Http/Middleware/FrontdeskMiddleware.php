<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class FrontdeskMiddleware
{
    public function handle($request, Closure $next)
    {
        // Check if user is logged in and has 'frontdesk' role
        if (Auth::check() && Auth::user()->usertype === 'frontdesk') {
            return $next($request);
        }

        // If not frontdesk, redirect to login
        return redirect('/login');
    }
}
