<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\Notifikasi;
use App\Services\DispensasiWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispensasiApprovalController extends Controller
{
    /**
     * Tampilkan Halaman Persetujuan Dispensasi (Akses dari WA Link / Web)
     */
    public function show($token)
    {
        $dispensasi = Dispensasi::with(['siswa.kelas', 'pembuat', 'pemroses'])
            ->where('token_approval', $token)
            ->orWhere('id', $token)
            ->firstOrFail();

        $user = Auth::user();

        // Pastikan hanya Waka yang bisa menyetujui
        if (! $user->isWaka()) {
            return redirect()->route('guru')->with('error', 'Halaman ini khusus untuk Wakasek Kesiswaan.');
        }

        return view('dashboard.dispensasi.approval', compact('dispensasi'));
    }

    /**
     * Proses keputusan Waka (Setujui / Tolak)
     */
    public function process(Request $request, Dispensasi $dispensasi, DispensasiWorkflowService $workflowService)
    {
        $user = Auth::user();

        if (! $user->isWaka()) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menyetujui dispensasi.');
        }

        $request->validate([
            'keputusan' => 'required|in:disetujui,ditolak',
            'catatan_waka' => 'nullable|string|max:500',
            'redirect_ke_dashboard' => 'nullable|boolean',
        ]);

        if ($dispensasi->status_waka !== 'menunggu') {
            return $this->approvalRedirect($request, $dispensasi)
                ->with('success', 'Dispensasi ini sudah diproses oleh Wakasek Kesiswaan.');
        }

        $keputusan = $request->keputusan;

        $dispensasi->update([
            'status_waka' => $keputusan,
            'status_akhir' => $keputusan,
            'diproses_oleh' => $user->id,
            'diproses_at' => now(),
            'catatan_waka' => $request->catatan_waka,
        ]);

        if ($keputusan === 'disetujui') {
            $workflowService->approve($dispensasi);
        }

        Notifikasi::query()
            ->where('id_dispensasi', $dispensasi->id)
            ->where('tipe', 'dispensasi_menunggu')
            ->update(['is_read' => true]);

        $statusText = $keputusan === 'disetujui' ? 'disetujui ✅' : 'ditolak ❌';

        return $this->approvalRedirect($request, $dispensasi)
            ->with('success', "Dispensasi untuk {$dispensasi->nama} berhasil {$statusText}.");
    }

    private function approvalRedirect(Request $request, Dispensasi $dispensasi)
    {
        if ($request->boolean('redirect_ke_dashboard')) {
            return redirect()->route('guru.utama');
        }

        return redirect()->route('dispensasi.approval', ['token' => $dispensasi->token_approval ?? $dispensasi->id]);
    }

    /**
     * Cetak Surat Dispensasi Resmi (Bisa diprint oleh Siswa / Piket / Waka)
     */
    public function cetakSurat(Dispensasi $dispensasi)
    {
        $dispensasi->load(['siswa.kelas', 'pembuat', 'pemroses']);

        return view('dashboard.dispensasi.cetak', compact('dispensasi'));
    }

    public function verify(string $token)
    {
        $dispensasi = Dispensasi::with(['siswa.kelas', 'pembuat', 'pemroses'])
            ->where('token_verifikasi', $token)
            ->where('status_akhir', 'disetujui')
            ->firstOrFail();

        return view('dashboard.dispensasi.verifikasi', compact('dispensasi'));
    }
}
