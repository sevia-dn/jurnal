<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class RedirectAuthenticatedByRole
{
    /**
     * Handle an incoming request.
     *
     * If the user is already authenticated, redirect them to the appropriate
     * dashboard based on their role. Otherwise allow the request to proceed.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Determine the correct redirect route for each role.
            // The role values are stored in the `role` column of the users table.
            switch ($user->role) {
                case 'admin':
                    return Redirect::route('dashboard');
                case 'pengurus_kelas':
                    return Redirect::route('pengurus-kelas.dashboard');
                case 'guru':
                case 'piket':
                case 'waka':
                    // All teaching‑related roles share the same entry point.
                    return Redirect::route('guru');
                default:
                    // Fallback – send them to the generic login page.
                    return Redirect::route('login');
            }
        }

        return $next($request);
    }
}
