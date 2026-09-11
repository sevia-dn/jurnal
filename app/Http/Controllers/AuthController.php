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

            switch ($user->role) {
                case 'admin':
                    return redirect()->route('dashboard.admin');

                case 'pengurus_kelas':
                    return redirect()->route('pengurus-kelas.jurnal.index');

                case 'guru':
                    return redirect()->route('guru');

                case 'piket':
                    return redirect()->route('dashboard.piket');

                case 'waka':
                    return redirect()->route('dashboard.kelas');

                default:
                    Auth::logout();
                    return back()->withErrors(['identity' => 'Role pengguna tidak memiliki hak akses.']);
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