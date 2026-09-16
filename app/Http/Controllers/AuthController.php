<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login (bisa pakai NIP atau username)
    public function login(Request $request)
    {
        $request->validate([
            'identity' => 'required|string',
            'password' => 'required|string',
        ]);

        $identity = $request->input('identity');
        $password = $request->input('password');

        // Cari user berdasarkan nip ATAU username
        $user = User::where('nip', $identity)
                    ->orWhere('username', $identity)
                    ->first();

        if ($user && Auth::attempt(['id' => $user->id, 'password' => $password])) {
            $request->session()->regenerate();

            // Jika ada intended URL (seperti link approval dari WA), prioritaskan ke intended URL
            switch ($user->role) {
                case 'admin':
                    return redirect()->intended(route('dashboard'));
                case 'pengurus_kelas':
                    return redirect()->intended(route('pengurus-kelas.dashboard'));
                case 'guru':
                case 'piket':
                case 'waka':
                    return redirect()->intended(route('guru'));
                default:
                    return redirect()->intended(route('login'));
            }
        }

        return back()->withErrors([
            'identity' => 'NIP/Username atau Password salah!',
        ])->onlyInput('identity');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}