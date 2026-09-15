<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranGuru;
use App\Models\Dispensasi;
use App\Models\Siswa;

class PiketController extends Controller
{
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
        return view('dashboard.piket.dispensasi', compact('siswas'));
    }

    // Simpan Pengajuan Dispensasi
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

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('dispensasi', 'public');
        }

        Dispensasi::create([
            'siswa_id' => $request->siswa_id,
            'jenis_dispensasi' => $request->jenis_dispensasi,
            'tanggal' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'bukti' => $buktiPath,
        ]);

        return redirect()->route('dashboard.piket.dispensasi.form')->with('success', 'Pengajuan dispensasi berhasil dikirim.');
    }
}
