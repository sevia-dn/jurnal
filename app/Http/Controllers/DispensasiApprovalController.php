<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispensasiApprovalController extends Controller
{
    /**
     * Tampilkan Halaman Persetujuan Dispensasi (Akses dari WA Link / Web)
     */
    public function show($token)
    {
        $dispensasi = Dispensasi::with(['siswa', 'pembuat'])
            ->where('token_approval', $token)
            ->orWhere('id', $token)
            ->firstOrFail();

        $user = Auth::user();

        // Pastikan hanya Waka yang bisa menyetujui
        if (!$user->isWaka() && $user->role !== 'admin') {
            return redirect()->route('guru')->with('error', 'Halaman ini khusus untuk Waka / Administrasi.');
        }

        return view('dashboard.dispensasi.approval', compact('dispensasi'));
    }

    /**
     * Proses keputusan Waka (Setujui / Tolak)
     */
    public function process(Request $request, Dispensasi $dispensasi)
    {
        $user = Auth::user();

        if (!$user->isWaka() && $user->role !== 'admin') {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menyetujui dispensasi.');
        }

        $request->validate([
            'keputusan' => 'required|in:disetujui,ditolak',
            'catatan_waka' => 'nullable|string|max:500',
        ]);

        $keputusan = $request->keputusan;

        $dispensasi->update([
            'status_waka' => $keputusan,
            'status_akhir' => $keputusan,
            'diproses_oleh' => $user->id,
            'diproses_at' => now(),
            'catatan_waka' => $request->catatan_waka,
        ]);

        $statusText = $keputusan === 'disetujui' ? 'disetujui' : 'ditolak';

        return redirect()->route('guru')->with('success', "Dispensasi untuk {$dispensasi->nama} berhasil {$statusText}.");
    }
}
