<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PiketScheduleService;
use Illuminate\Http\RedirectResponse;
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
        // Jika pengguna sudah login, arahkan ke dashboard sesuai role.
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
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
}
