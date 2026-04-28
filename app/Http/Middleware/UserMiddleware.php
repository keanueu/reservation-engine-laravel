<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && property_exists(Auth::user(), 'usertype') && Auth::user()->usertype === 'user') {
            return $next($request);
        }
        return redirect('/login');
    }
}
