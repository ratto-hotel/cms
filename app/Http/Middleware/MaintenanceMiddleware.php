<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        $allowed_pages = $request->is('maintenance') || $request->is('settings.two-factor')
        || $request->is('two-factor.verify');

        // If not authenticated, always redirect to maintenance if enabled
        if ((!$allowed_pages && $request->method() !== 'POST') && (!Auth::check() && setting('maintenance_enabled') === '1')) {
            return to_route('maintenance.show');
        }

        // If authenticated user is below the minimum maintenance rank
        if ((setting('maintenance_enabled') === '1' && !$request->is('maintenance')) && (Auth::check() && setting('min_maintenance_login_rank') > Auth::user()->rank)) {
            Auth::logout(); // Logout the authenticated user

            return to_route('maintenance.show');
        }

        // If authenticated user is above or equal the minimum maintenance rank
        if ((setting('maintenance_enabled') === '1' && !$allowed_pages) && (Auth::check() && Auth::user()->rank >= setting('min_maintenance_login_rank'))) {
            return $next($request);
        }

        return $next($request);
    }
}
