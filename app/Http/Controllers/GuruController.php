<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\KehadiranGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $isPiketActive = $user ? $user->isPiketActive() : false;
        $isWaka = $user ? $user->isWaka() : false;

        // Cek apakah guru sudah absen masuk hari ini
        $today = now()->toDateString();
        $kehadiranHariIni = $user ? KehadiranGuru::where('user_id', $user->id)->whereDate('tanggal', $today)->first() : null;
        $sudahAbsen = $kehadiranHariIni !== null;

        $pendingDispensasis = [];
        if ($isWaka) {
            $pendingDispensasis = Dispensasi::with(['siswa', 'pembuat'])
                ->where('status_waka', 'menunggu')
                ->latest()
                ->get();
        }

        $allDispensasis = Dispensasi::with(['siswa', 'pembuat', 'pemroses'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.guru-pengajar.utama', compact(
            'user',
            'isPiketActive',
            'isWaka',
            'pendingDispensasis',
            'allDispensasis',
            'sudahAbsen',
            'kehadiranHariIni'
        )); 
    }

    /**
     * Proses Absen Masuk / Lapor Kehadiran Guru
     */
    public function absenMasuk(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $today = now()->toDateString();
        $jamSekarang = now()->format('H:i:s');

        // Gunakan updateOrCreate agar tidak duplicate jika diklik 2x
        KehadiranGuru::updateOrCreate(
            [
                'user_id' => $user->id,
                'tanggal' => $today,
            ],
            [
                'jam_masuk' => $jamSekarang,
                'status' => 'Hadir',
            ]
        );

        return back()->with('success', 'Kehadiran masuk berhasil dilaporkan pada pukul ' . substr($jamSekarang, 0, 5) . ' WIB.');
    }
}

