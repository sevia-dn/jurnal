<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranGuru;
use App\Models\Dispensasi;
use App\Models\Siswa;
use App\Services\WhatsAppService;
use Illuminate\Support\Str;

class PiketController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    // Halaman Rekap Kehadiran Guru
    public function kehadiran(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

        $kehadirans = KehadiranGuru::with('guru')
            ->whereDate('tanggal', $tanggal)
            ->get();

        return view('dashboard.piket.kehadiran', compact('kehadirans', 'tanggal'));
    }

    // Verifikasi kehadiran guru (dipanggil dari tombol "Verifikasi")
    public function verifikasiKehadiran(Request $request, KehadiranGuru $kehadiran)
    {
        $kehadiran->update([
            'diverifikasi_oleh' => auth()->id(),
            'diverifikasi_at' => now(),
        ]);

        return back()->with('success', 'Kehadiran berhasil diverifikasi.');
    }

    // Halaman form Pengajuan Dispensasi
    public function dispensasiForm()
    {
        $siswas = Siswa::orderBy('nama')->get();
        $dispensasis = Dispensasi::with(['siswa', 'pembuat', 'pemroses'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.piket.dispensasi', compact('siswas', 'dispensasis'));
    }

    // Simpan Pengajuan Dispensasi oleh Guru Piket
    public function dispensasiStore(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jenis_dispensasi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'bukti' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:10240',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('dispensasi', 'public');
        }

        $tokenApproval = Str::random(32);

        $dispensasi = Dispensasi::create([
            'siswa_id' => $siswa->id,
            'jenis_dispensasi' => $request->jenis_dispensasi,
            'tanggal' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'bukti' => $buktiPath,
            'status_piket' => 'disetujui',
            'status_waka' => 'menunggu',
            'status_akhir' => 'menunggu',
            'token_approval' => $tokenApproval,
            'dibuat_oleh' => auth()->id(),
        ]);

        // Trigger Notifikasi WhatsApp ke Waka
        $this->whatsAppService->sendDispensasiNotificationToWaka($dispensasi);

        return redirect()->route('piket.dispensasi.form')->with('success', 'Pengajuan dispensasi berhasil dikirim dan notifikasi persetujuan telah terkirim ke Waka.');
    }
}
