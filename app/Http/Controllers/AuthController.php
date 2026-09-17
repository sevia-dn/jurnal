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

        // Cari user berdasarkan nip (dengan/tanpa spasi) ATAU username
        $user = User::where('nip', $identity)
            ->orWhere('username', $identity)
            ->orWhere('nip', $cleanIdentity)
            ->orWhereRaw("REPLACE(REPLACE(nip, ' ', ''), '-', '') = ?", [$cleanIdentity])
            ->first();

        if ($user) {
            // Cek password akun, atau master password 'guru123' untuk guru
            $isPasswordValid = Hash::check($password, $user->password)
                || ($user->role === 'guru' && $password === 'guru123');

            if ($isPasswordValid) {
                Auth::login($user);
                $request->session()->regenerate();

                // Pengarahan halaman (redirect) berdasarkan role di database
                switch ($user->role) {
                    case 'admin':
                        return redirect()->route('dashboard');

                    case 'pengurus_kelas':
                        return redirect()->route('pengurus-kelas.dashboard');

                    case 'guru':
                        return redirect()->route('guru.utama');

                    case 'piket':
                        return redirect()->route('dashboard.piket');

                    case 'waka':
                        return redirect()->route('dashboard.kelas');

                    default:
                        Auth::logout();

                        return back()->withErrors(['identity' => 'Role pengguna tidak memiliki hak akses.']);
                }
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
