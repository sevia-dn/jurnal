<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestSekretarisController extends Controller
{
    public function dashboard()
    {
        return view('sekretaris.dashboard', [
            'tanggal'           => 'Kamis, 24 Agustus 2023',
            'total_sesi'        => 10,
            'perlu_persetujuan' => DB::table('jurnal_mengajars')->whereNull('catatan')->count(),
            'kehadiran_guru'    => '3/4',
            'kehadiran_siswa'   => '30/36'
        ]);
    }

    // 1. Antrean Approval (Jurnal yang catatan-nya masih NULL)
    public function indexJurnal()
    {
        $jurnals = DB::table('jurnal_mengajars')
                    ->whereNull('catatan')
                    ->get(); 

        return view('sekretaris.jurnal_approve', compact('jurnals'));
    }

    // 2. Eksekusi Approve Jurnal
    public function approveJurnal(Request $request, $id)
    {
        $request->validate([
            'status_kehadiran_guru' => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan',
            'catatan'               => 'nullable|string',
        ]);

        // KUNCI PERBAIKAN: Jika catatan tidak diisi sekretaris, otomatis diisi '-' agar NOT NULL
        $catatanFinal = $request->catatan ? $request->catatan : '-';

        // Update data di database
        DB::table('jurnal_mengajars')
            ->where('id_jurnal', $id)
            ->update([
                'status_kehadiran_guru' => $request->status_kehadiran_guru,
                'catatan'               => $catatanFinal,
            ]);

        // Langsung diarahkan ke halaman history agar hasil update terlihat
        return redirect()->route('sekretaris.history')->with('success', 'Jurnal berhasil disetujui!');
    }

    // 3. Riwayat Jurnal (Jurnal yang catatan-nya sudah terisi / NOT NULL)
    public function historyJurnal()
    {
        $jurnals = DB::table('jurnal_mengajars')
                    ->whereNotNull('catatan')
                    ->get();

        return view('sekretaris.jurnal_history', compact('jurnals'));
    }
}