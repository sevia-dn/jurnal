<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Services\PiketScheduleService;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm(WhatsAppService $whatsAppService)
    {
        // Jika pengguna sudah login, arahkan ke dashboard sesuai role.
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        $adminWaNumber = $whatsAppService->getAdminNumber();
        $adminWaUrl = $whatsAppService->formatWhatsAppUrl(
            $adminWaNumber,
            'Halo Admin JurnalKita, saya butuh bantuan terkait lupa password akun saya.'
        );

        return view('auth.login', compact('adminWaNumber', 'adminWaUrl'));
    }

    /**
     * Redirect berdasarkan role pengguna.
     */
    private function redirectByRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->route('dashboard'),
            'pengurus_kelas' => redirect()->route('pengurus-kelas.dashboard'),
            'guru', 'piket', 'waka' => redirect()->route('guru'),
            default => redirect()->route('login'),
        };
    }

    /**
     * Proses login (bisa menggunakan NIP dengan/tanpa spasi, atau username).
     */
    public function login(Request $request, PiketScheduleService $piketScheduleService)
    {
        $request->validate([
            'identity' => 'required|string',
            'password' => 'required|string',
        ]);

        $identity = trim($request->input('identity'));
        $password = $request->input('password');
        $cleanIdentity = str_replace([' ', '-', '.'], '', $identity);
        $normalizedUsername = strtolower($identity);

        // Username diprioritaskan karena nilainya sudah ditetapkan secara eksplisit pada UserSeeder.
        $user = User::whereRaw('LOWER(username) = ?', [$normalizedUsername])->first();

        if (! $user) {
            $user = User::where('nip', $identity)
                ->orWhere('nip', $cleanIdentity)
                ->orWhereRaw("REPLACE(REPLACE(REPLACE(nip, ' ', ''), '-', ''), '.', '') = ?", [$cleanIdentity])
                ->first();
        }

        if ($user) {
            $isPasswordValid = Hash::check($password, $user->password);

            if ($isPasswordValid) {
                Auth::login($user);
                $request->session()->regenerate();

                // Jika ada intended URL (seperti link approval dari WA), prioritaskan ke intended URL.
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

    /**
     * Ajukan permohonan reset password dari pengguna ke Admin via WhatsApp.
     */
    public function requestPasswordReset(Request $request, WhatsAppService $whatsAppService)
    {
        $request->validate([
            'identity' => 'required|string',
            'no_hp' => 'nullable|string|max:25',
            'alasan' => 'nullable|string|max:500',
        ], [
            'identity.required' => 'NIP atau Username wajib diisi.',
        ]);

        $identity = trim($request->input('identity'));
        $cleanIdentity = str_replace([' ', '-', '.'], '', $identity);
        $normalizedUsername = strtolower($identity);

        // Cari user yang sesuai di database
        $user = User::whereRaw('LOWER(username) = ?', [$normalizedUsername])
            ->orWhere('nip', $identity)
            ->orWhere('nip', $cleanIdentity)
            ->orWhereRaw("REPLACE(REPLACE(REPLACE(nip, ' ', ''), '-', ''), '.', '') = ?", [$cleanIdentity])
            ->first();

        $nama = $user ? $user->name : $identity;
        $username = $user ? $user->username : $identity;
        $role = $user ? ucfirst(str_replace('_', ' ', $user->role)) : 'Pengguna';
        $userId = $user ? $user->id : null;
        $noHp = $request->input('no_hp') ?: ($user?->no_hp);
        $alasan = $request->input('alasan') ?: 'Lupa kata sandi lama, meminta bantuan reset password.';

        // Buat record laporan permohonan reset password di database
        PasswordResetRequest::create([
            'user_id' => $userId,
            'nama' => $nama,
            'username' => $username,
            'role' => $user?->role ?? 'guru',
            'no_hp' => $noHp,
            'alasan' => $alasan,
            'status' => 'menunggu',
        ]);

        // Buat template pesan WhatsApp ke Admin
        $pesanWa = $whatsAppService->buildPasswordResetRequestMessage(
            $nama,
            $username,
            $role,
            $noHp,
            $alasan
        );

        $adminNumber = $whatsAppService->getAdminNumber();
        $targetWaUrl = $whatsAppService->formatWhatsAppUrl($adminNumber, $pesanWa);

        // Coba kirim via gateway jika gateway aktif
        $whatsAppService->sendMessage($adminNumber, $pesanWa);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'wa_url' => $targetWaUrl,
                'message' => 'Permohonan berhasil dicatat dan sedang dialihkan ke WhatsApp Admin.',
            ]);
        }

        return redirect()->away($targetWaUrl);
    }
}
