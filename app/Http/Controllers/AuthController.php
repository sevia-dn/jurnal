<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses data inputan login (bisa pakai username atau nip)
    public function login(Request $request)
    {
        $inputField = $request->has('identity') ? 'identity' : 'login';

        $request->validate([
            $inputField => ['required'],
            'password' => ['required'],
        ], [
            "{$inputField}.required" => 'Kolom Username atau NIP wajib diisi.',
            'password.required' => 'Kolom Password wajib diisi.',
        ]);

        $identity = $request->input($inputField);
        $password = $request->input('password');

        // Cari user berdasarkan kolom username ATAU nip di database
        $user = User::where('username', $identity)
                    ->orWhere('nip', $identity)
                    ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            switch ($user->role) {
                case 'admin':
                    return redirect()->route('dashboard');

                case 'pengurus_kelas':
                    return redirect()->route('pengurus-kelas.jurnal.index');

                case 'guru':
                    // Jika hari ini bertugas sebagai Guru Piket, otomatis masuk ke dashboard piket
                    if ($user->isPiketHariIni()) {
                        return redirect()->route('dashboard.piket')->with('info', 'Selamat bertugas! Hari ini Anda bertugas sebagai Guru Piket.');
                    }
                    return redirect()->route('dashboard.guru');

                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            $inputField => 'Username/NIP atau password salah.',
        ])->withInput($request->only($inputField));
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Permintaan Reset Password dari Halaman Login
    public function kirimLaporanReset(Request $request)
    {
        $identity = $request->input('login') ?? $request->input('identity');

        if (empty($identity)) {
            return back()->with('error_reset', 'NIP atau Username wajib diisi untuk mengajukan reset password.')
                         ->with('open_reset_modal', true);
        }

        $user = User::where('username', $identity)
                    ->orWhere('nip', $identity)
                    ->first();

        if (!$user) {
            return back()->with('error_reset', 'Akun dengan Username atau NIP tersebut tidak ditemukan.')
                         ->with('open_reset_modal', true);
        }

        // Cek jika sudah ada laporan berstatus menunggu
        $existing = PasswordResetRequest::where('user_id', $user->id)
                                        ->where('status', 'menunggu')
                                        ->first();

        if ($existing) {
            return back()->with('info_reset', 'Permohonan ganti password untuk akun ini sudah ada dan sedang menunggu respon dari Admin.')
                         ->with('open_reset_modal', true);
        }

        PasswordResetRequest::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'username' => $user->username,
            'role' => $user->role ?? 'guru',
            'no_hp' => $user->no_hp,
            'alasan' => $request->input('alasan') ?: 'Lupa kata sandi lama, meminta bantuan reset password ke Admin.',
            'status' => 'menunggu',
        ]);

        return back()->with('success_reset', 'Permintaan ganti password berhasil terkirim ke Admin! Notifikasi telah masuk ke sistem Administrator.')
                     ->with('open_reset_modal', true);
    }
}