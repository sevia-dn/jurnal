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
    public function showLoginForm() {
        return view('auth.login');
    }

    // Memproses data inputan login (bisa pakai username atau nip)
    public function login(Request $request) 
    {
        // 1. Validasi input form
        $request->validate([
            'login' => ['required'],
            'password' => ['required', 'min:6'],
        ], [
            'login.required' => 'Kolom Username atau NIP wajib diisi.',
            'password.required' => 'Kolom Password wajib diisi.',
            'password.min' => 'Password minimal harus terdiri dari 6 karakter.',
        ]);

        // 2. Cari user berdasarkan kolom username ATAU nip di database
        $user = User::where('username', $request->login)
                    ->orWhere('nip', $request->login)
                    ->first();

        // 3. Cek apakah user ditemukan dan passwordnya cocok
        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->status === 'nonaktif') {
                return back()->withErrors([
                    'login' => 'Akun ini sedang dinonaktifkan. Silakan hubungi Administrator.',
                ])->withInput($request->only('login'));
            }

            Auth::login($user);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            // Login admin langsung diarahkan ke Dashboard Admin
            return redirect()->route('dashboard');
        }

        // 4. Jika gagal, kembalikan ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'login' => 'Username/NIP atau password salah.',
        ])->withInput($request->only('login'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function kirimLaporanReset(Request $request)
    {
        $request->validate([
            'login' => ['required'],
            'alasan' => ['nullable', 'string', 'max:500'],
        ], [
            'login.required' => 'NIP atau Username wajib diisi untuk mengajukan reset password.',
        ]);

        $user = User::where('username', $request->login)
                    ->orWhere('nip', $request->login)
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
            return back()->with('info_reset', 'Laporan reset password untuk akun ini sudah ada dan sedang menunggu respon dari Admin.')
                         ->with('open_reset_modal', true);
        }

        PasswordResetRequest::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'no_hp' => $user->no_hp,
            'alasan' => $request->alasan ?? 'Permintaan reset password melalui halaman login',
            'status' => 'menunggu',
            'created_at' => now(),
        ]);

        return back()->with('success_reset', 'Laporan permintaan reset password berhasil dikirim ke Admin! Silakan tunggu admin menyetujui dan mengganti password Anda.')
                     ->with('open_reset_modal', true);
    }
}