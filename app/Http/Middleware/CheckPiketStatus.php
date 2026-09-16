<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPiketStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
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
