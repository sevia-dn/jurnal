<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPiketStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->role === 'guru' && $user->isPiketActive()) {
            // Jika guru sedang dalam mode piket aktif dan mencoba mengisi logbook mengajar
            if ($request->routeIs('guru.logbook*') || $request->is('guru-pengajar/logbook*')) {
                return redirect()->route('guru.utama')->with('warning', 'Logbook mengajar terkunci karena Anda sedang dalam jadwal piket hari ini.');
            }
        }

        return $next($request);
    }
}
