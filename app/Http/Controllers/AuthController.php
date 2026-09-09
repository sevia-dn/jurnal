<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
public function login(Request $request)
{
    $request->validate([
        'identity' => 'required|string',
        'password' => 'required|string',
    ]);

    $identity = $request->input('identity');
    $password = $request->input('password');

    // Cari user berdasarkan nip ATAU username
    $user = \App\Models\User::where('nip', $identity)
                ->orWhere('username', $identity)
                ->first();

    if ($user && Auth::attempt(['id' => $user->id, 'password' => $password])) {
        $request->session()->regenerate();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard');
            case 'sekretaris':
                return redirect()->route('sekretaris.jurnal.index');
            case 'guru':
                return redirect()->route('dashboard.guru');
            case 'piket':
            case 'guru_piket':
                return redirect()->route('dashboard.jadwal');
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
