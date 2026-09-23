<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login (bisa menggunakan NIP dengan/tanpa spasi, atau username).
     */
    public function login(Request $request)
    {
        $request->validate([
            'identity' => 'required|string',
            'password' => 'required|string',
        ]);

        $identity = trim($request->input('identity'));
        $password = $request->input('password');
        $cleanIdentity = str_replace([' ', '-', '.'], '', $identity);

        $user = User::where('nip', $identity)
            ->orWhere('username', $identity)
            ->orWhere('nip', $cleanIdentity)
            ->orWhereRaw("REPLACE(REPLACE(nip, ' ', ''), '-', '') = ?", [$cleanIdentity])
            ->first();

        if ($user) {
            $isPasswordValid = Hash::check($password, $user->password)
                || ($user->role === 'guru' && $password === 'guru123');

            // ← TAMBAHKAN INI: kalau password gak valid, tolak di sini juga
            if (!$isPasswordValid) {
                return back()->withErrors([
                    'identity' => 'NIP/Username atau Password salah!',
                ])->onlyInput('identity');
            }

            // ← TAMBAHKAN INI: baris paling penting yang hilang
            Auth::login($user);

            switch ($user->role) {
                case 'admin':
                    return redirect()->intended(route('dashboard'));
                case 'pengurus_kelas':
                    return redirect()->intended(route('pengurus-kelas.dashboard'));
                case 'guru':
                case 'piket':
                case 'waka':
                    return redirect()->intended(route('guru.utama'));   
                default:
                    return redirect()->intended(route('login'));
            }
        }

        return back()->withErrors([
            'identity' => 'NIP/Username atau Password salah!',
        ])->onlyInput('identity');
    }
    /**
     * Proses logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
