<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JurnalMengajar;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    /**
     * Menyimpan Jurnal Pembelajaran dan Rekap Absensi Siswa oleh Guru
     */
    public function store(Request $request)
    {
        // Validasi input form jurnal dan absensi siswa
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mapels,id',
            'jam_ke' => 'required|integer',
            'materi' => 'required|string|max:255',
            'ada_tugas' => 'required|in:Ya,Tidak',
            'catatan' => 'nullable|string',
            
            // Absensi siswa berbentuk array [id_siswa => status]
            'absensi' => 'required|array',
            'absensi.*' => 'required|in:Hadir,Sakit,Izin,Alpa',
        ]);

        $user = Auth::user();
        $todayDate = Carbon::today()->toDateString();

        // Cek apakah guru sudah mengirimkan jurnal hari ini
        $existing = JurnalMengajar::where('id_user', $user->id)
            ->where('tanggal', $todayDate)
            ->exists();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah mengirimkan jurnal pembelajaran untuk hari ini.');
        }

        // Hitung rekap jumlah kehadiran siswa secara otomatis dari array input
        $absensiData = $request->input('absensi');
        $jmlHadir = 0;
        $jmlSakit = 0;
        $jmlIzin = 0;
        $jmlAlpa = 0;

        foreach ($absensiData as $status) {
            switch ($status) {
                case 'Hadir': 
                    $jmlHadir++; 
                    break;
                case 'Sakit': 
                    $jmlSakit++; 
                    break;
                case 'Izin': 
                    $jmlIzin++; 
                    break;
                case 'Alpa': 
                    $jmlAlpa++; 
                    break;
            }
        }
        $jmlTidakHadir = $jmlSakit + $jmlIzin + $jmlAlpa;

        // Gunakan Database Transaction agar penyimpanan data konsisten (Jurnal & Absensi Siswa)
        DB::beginTransaction();
        try {
            // 1. Simpan data utama ke tabel jurnal_mengajars
            $jurnal = JurnalMengajar::create([
                'id_user' => $user->id,
                'id_kelas' => $request->id_kelas,
                'id_mapel' => $request->id_mapel,
                'tanggal' => $todayDate,
                'jam_ke' => $request->jam_ke,
                'materi' => $request->materi,
                'jumlah_hadir' => $jmlHadir,
                'jumlah_sakit' => $jmlSakit,
                'jumlah_izin' => $jmlIzin,
                'jumlah_alpa' => $jmlAlpa,
                'jumlah_tidak_hadir' => $jmlTidakHadir,
                'status_kehadiran_guru' => 'Hadir',
                'ada_tugas' => $request->ada_tugas === 'Ya',
                'catatan' => $request->catatan,
            ]);

            // 2. Simpan detail absensi masing-masing siswa ke tabel absensis
            foreach ($absensiData as $idSiswa => $statusSiswa) {
                Absensi::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_siswa' => $idSiswa,
                    'status' => $statusSiswa,
                ]);
            }

            DB::commit();
            return redirect()->route('guru.riwayat')->with('success', 'Jurnal pembelajaran dan absensi siswa berhasil dikirim!');
        
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan Halaman Riwayat Jurnal Mengajar Guru
     */
    public function history()
    {
        $user = Auth::user();
        
        // Ambil data riwayat jurnal milik guru yang sedang login, diurutkan dari yang terbaru
        $riwayatJurnals = JurnalMengajar::with(['kelas', 'mapel', 'absensis'])
            ->where('id_user', $user->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_ke', 'desc')
            ->get();

        return view('dashboard.guru-pengajar.riwayat', compact('riwayatJurnals'));
    }
}