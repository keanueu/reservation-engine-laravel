<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Auth;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Default home path (fallback)
     */
    public const HOME = '/redirect';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        // Dynamic redirect after login
        Fortify::redirects('login', function () {
            $user = Auth::user();

            if (!$user) {
                return '/'; // fallback if not logged in
            }

            $usertype = strtolower(trim($user->usertype ?? ''));

            switch ($usertype) {
                case 'admin':
                    return '/admin/dashboard';
                case 'frontdesk':
                    return '/frontdesk/home';
                default:
                    return '/'; // regular user home
            }
        });

        // Define the routes
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
