<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\Notifikasi;
use App\Models\User;
use App\Services\DispensasiWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispensasiApprovalController extends Controller
{
    /**
     * Tampilkan halaman persetujuan dari tautan WhatsApp tanpa login.
     * Token persetujuan acak bertindak sebagai kredensial akses khusus pengajuan.
     */
    public function showFromWhatsApp(Request $request, string $token)
    {
        $dispensasi = $this->findByApprovalToken($token);
        $selectedWaka = $this->wakaFromLink($request);

        return view('dashboard.dispensasi.approval', [
            'dispensasi' => $dispensasi,
            'isPublicApproval' => true,
            'wakaKesiswaans' => User::wakaKesiswaan()->orderBy('name')->get(['id', 'name']),
            'selectedWaka' => $selectedWaka,
        ]);
    }

    /**
     * Tampilkan Halaman Persetujuan Dispensasi (Akses dari WA Link / Web)
     */
    public function show(string $token)
    {
        $dispensasi = Dispensasi::with(['siswa.kelas', 'siswas.kelas', 'pembuat', 'pemroses'])
            ->where('token_approval', $token)
            ->orWhere('id', $token)
            ->firstOrFail();

        $user = Auth::user();

        // Pastikan hanya Waka yang bisa menyetujui
        if (! $user->isWaka()) {
            return redirect()->route('guru')->with('error', 'Halaman ini khusus untuk Wakasek Kesiswaan.');
        }

        return view('dashboard.dispensasi.approval', [
            'dispensasi' => $dispensasi,
            'isPublicApproval' => false,
        ]);
    }

    /**
     * Proses keputusan dari tautan WhatsApp yang memiliki token persetujuan.
     */
    public function processFromWhatsApp(Request $request, string $token, DispensasiWorkflowService $workflowService)
    {
        $dispensasi = $this->findByApprovalToken($token);

        $validated = $this->validateDecision($request, true);

        if ($dispensasi->status_waka !== 'menunggu') {
            return redirect()->route('waka.dispensasi.show', ['token' => $dispensasi->token_approval])
                ->with('success', 'Dispensasi ini sudah diproses oleh Wakasek Kesiswaan.');
        }

        $waka = User::wakaKesiswaan()->findOrFail($validated['waka_id']);

        $this->completeDecision($dispensasi, $validated, $workflowService, $waka->id);

        $statusText = $validated['keputusan'] === 'disetujui' ? 'disetujui ✅' : 'ditolak ❌';

        return redirect()->route('waka.dispensasi.show', ['token' => $dispensasi->token_approval])
            ->with('success', "Dispensasi untuk {$dispensasi->nama} berhasil {$statusText}.");
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

        $validated = $this->validateDecision($request);

        if ($dispensasi->status_waka !== 'menunggu') {
            return $this->approvalRedirect($request, $dispensasi)
                ->with('success', 'Dispensasi ini sudah diproses oleh Wakasek Kesiswaan.');
        }

        $this->completeDecision($dispensasi, $validated, $workflowService, $user->id);

        $statusText = $validated['keputusan'] === 'disetujui' ? 'disetujui ✅' : 'ditolak ❌';

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
     * @return array{keputusan: string, catatan_waka?: string|null, redirect_ke_dashboard?: bool, waka_id?: int}
     */
    private function validateDecision(Request $request, bool $requiresWakaIdentity = false): array
    {
        $rules = [
            'keputusan' => 'required|in:disetujui,ditolak',
            'catatan_waka' => 'nullable|string|max:500',
            'redirect_ke_dashboard' => 'nullable|boolean',
        ];

        if ($requiresWakaIdentity) {
            $rules['waka_id'] = 'required|integer|exists:users,id';
        }

        return $request->validate($rules);
    }

    /**
     * @param  array{keputusan: string, catatan_waka?: string|null, redirect_ke_dashboard?: bool, waka_id?: int}  $validated
     */
    private function completeDecision(
        Dispensasi $dispensasi,
        array $validated,
        DispensasiWorkflowService $workflowService,
        ?int $processedBy = null,
    ): void {
        $dispensasi->update([
            'status_waka' => $validated['keputusan'],
            'status_akhir' => $validated['keputusan'],
            'diproses_oleh' => $processedBy,
            'diproses_at' => now(),
            'catatan_waka' => $validated['catatan_waka'] ?? null,
        ]);

        if ($validated['keputusan'] === 'disetujui') {
            $workflowService->approve($dispensasi);
        }

        Notifikasi::query()
            ->where('id_dispensasi', $dispensasi->id)
            ->where('tipe', 'dispensasi_menunggu')
            ->update(['is_read' => true]);
    }

    private function findByApprovalToken(string $token): Dispensasi
    {
        return Dispensasi::with(['siswa.kelas', 'siswas.kelas', 'pembuat', 'pemroses'])
            ->where('token_approval', $token)
            ->firstOrFail();
    }

    private function wakaFromLink(Request $request): ?User
    {
        $username = $request->string('waka')->trim()->toString();
        if ($username === '') {
            return null;
        }

        return User::wakaKesiswaan()
            ->whereRaw('LOWER(username) = ?', [mb_strtolower($username)])
            ->first();
    }

    /**
     * Cetak Surat Dispensasi Resmi (Bisa diprint oleh Siswa / Piket / Waka)
     */
    public function cetakSurat(Dispensasi $dispensasi)
    {
        $dispensasi->load(['siswa.kelas', 'siswas.kelas', 'pembuat', 'pemroses']);

        return view('dashboard.dispensasi.cetak', compact('dispensasi'));
    }

    /**
     * Cetak surat dari tautan persetujuan Waka tanpa memerlukan sesi login.
     */
    public function cetakSuratFromWhatsApp(string $token)
    {
        $dispensasi = $this->findByApprovalToken($token);
        abort_unless(in_array($dispensasi->status_waka, ['disetujui', 'approved'], true), 404);

        return view('dashboard.dispensasi.cetak', compact('dispensasi'));
    }

    public function verify(string $token)
    {
        $dispensasi = Dispensasi::with(['siswa.kelas', 'siswas.kelas', 'pembuat', 'pemroses'])
            ->where('token_verifikasi', $token)
            ->where('status_akhir', 'disetujui')
            ->firstOrFail();

        return view('dashboard.dispensasi.verifikasi', compact('dispensasi'));
    }
}
