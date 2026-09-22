<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengurusKelasController extends Controller
{
    /**
     * Mendapatkan model Kelas yang terkait dengan akun Pengurus Kelas yang sedang login
     */
    protected function getKelasPengurus(): ?Kelas
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }

        // Contoh: name "Pengurus Kelas X TKI 1" -> "X TKI 1"
        $cleanName = trim(str_ireplace('Pengurus Kelas ', '', $user->name));

        return Kelas::where('nama_kelas', $cleanName)
            ->orWhere('nama_kelas', $user->name)
            ->first();
    }

    /**
     * Dashboard Utama Pengurus Kelas
     */
    public function dashboard()
    {
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        $hariIni = $now->translatedFormat('l');
        $tanggalFormatted = $now->translatedFormat('l, d F Y');

        $kelas = $this->getKelasPengurus();
        $kelasId = $kelas?->id_kelas;

        // 1. Total Sesi Hari Ini
        $totalSesi = 0;
        if ($kelasId) {
            $totalSesi = JadwalMengajar::where('id_kelas', $kelasId)
                ->where('hari', $hariIni)
                ->count();
        }

        // 2. Jurnal Perlu Persetujuan (belum_divalidasi)
        $perluPersetujuanQuery = JurnalMengajar::where('status_validasi', 'belum_divalidasi');
        if ($kelasId) {
            $perluPersetujuanQuery->where('id_kelas', $kelasId);
        }
        $perluPersetujuan = $perluPersetujuanQuery->count();

        // 3. Kehadiran Guru Hari Ini
        $guruTercatat = 0;
        if ($kelasId) {
            $guruTercatat = JurnalMengajar::where('id_kelas', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->count();
        }
        $kehadiranGuruText = "{$guruTercatat}/".max(1, $totalSesi);

        // 4. Kehadiran Siswa
        $totalSiswa = $kelas?->jumlah_siswa ?? ($kelasId ? Siswa::where('kelas_id', $kelasId)->count() : 36);
        $siswaHadirTerakhir = 0;
        $jurnalTerakhir = null;
        if ($kelasId) {
            $jurnalTerakhir = JurnalMengajar::where('id_kelas', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->latest('id_jurnal')
                ->first();
            if ($jurnalTerakhir) {
                $siswaHadirTerakhir = $jurnalTerakhir->jumlah_hadir;
            }
        }
        $kehadiranSiswaText = $jurnalTerakhir ? "{$siswaHadirTerakhir}/{$totalSiswa}" : "-/{$totalSiswa}";

        // Daftar jurnal yang menunggu validasi (untuk quick action di dashboard)
        $jurnalAntrean = JurnalMengajar::with(['user', 'mapel', 'kelas'])
            ->when($kelasId, fn ($q) => $q->where('id_kelas', $kelasId))
            ->where('status_validasi', 'belum_divalidasi')
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_ke', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.pengurus-kelas.utama', compact(
            'kelas',
            'tanggalFormatted',
            'totalSesi',
            'perluPersetujuan',
            'kehadiranGuruText',
            'kehadiranSiswaText',
            'jurnalAntrean'
        ));
    }

    /**
     * Jadwal & Jurnal Pelajaran Kelas
     */
    public function jadwal(Request $request)
    {
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        $hariIni = $now->translatedFormat('l');
        $tanggalFormatted = $now->translatedFormat('l, d F Y');

        $kelas = $this->getKelasPengurus();
        $kelasId = $kelas?->id_kelas;

        $jadwals = collect();
        if ($kelasId) {
            $jadwals = JadwalMengajar::with(['user', 'mapel', 'kelas'])
                ->where('id_kelas', $kelasId)
                ->where('hari', $hariIni)
                ->orderBy('jam_mulai')
                ->get();
        }

        // Cek logbook yang sudah terisi untuk setiap sesi jadwal hari ini
        $jurnalHariIni = collect();
        if ($kelasId) {
            $jurnalHariIni = JurnalMengajar::where('id_kelas', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->get()
                ->keyBy('jam_ke');
        }

        return view('dashboard.pengurus-kelas.jadwal', compact(
            'kelas',
            'hariIni',
            'tanggalFormatted',
            'jadwals',
            'jurnalHariIni'
        ));
    }

    /**
     * Daftar Jurnal / Logbook yang Perlu Divalidasi / Riwayat Logbook Kelas
     */
    public function jurnalIndex(Request $request)
    {
        $kelas = $this->getKelasPengurus();
        $kelasId = $kelas?->id_kelas;
        $status = $request->query('status', 'belum_divalidasi');

        $query = JurnalMengajar::with(['user', 'mapel', 'kelas', 'absensis.siswa'])
            ->when($kelasId, fn ($q) => $q->where('id_kelas', $kelasId));

        if ($status === 'belum_divalidasi') {
            $query->where('status_validasi', 'belum_divalidasi');
        } elseif ($status === 'disetujui') {
            $query->where('status_validasi', 'disetujui');
        } elseif ($status === 'ditolak') {
            $query->where('status_validasi', 'ditolak');
        }

        $jurnals = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_ke', 'desc')
            ->get();

        // Jika hanya 1 jurnal ditemukan atau ingin direct ke detail
        $jurnal = $jurnals->first();

        return view('dashboard.pengurus-kelas.jurnal-detail', compact(
            'kelas',
            'jurnals',
            'jurnal',
            'status'
        ));
    }

    /**
     * Detail Spesifik Jurnal Mengajar untuk Validasi oleh Pengurus Kelas
     */
    public function jurnalDetail($id)
    {
        $kelas = $this->getKelasPengurus();
        $kelasId = $kelas?->id_kelas;

        $jurnal = JurnalMengajar::with(['user', 'mapel', 'kelas', 'absensis.siswa'])
            ->when($kelasId, fn ($q) => $q->where('id_kelas', $kelasId))
            ->findOrFail($id);

        return view('dashboard.pengurus-kelas.jurnal-detail', compact(
            'kelas',
            'jurnal'
        ));
    }

    /**
     * Validasi / Setujui / Minta Revisi Logbook oleh Pengurus Kelas
     */
    public function validasiJurnal(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:setujui,tolak',
            'catatan_validasi' => 'nullable|string|max:500',
        ]);

        $jurnal = JurnalMengajar::findOrFail($id);

        $status = $request->action === 'setujui' ? 'disetujui' : 'ditolak';
        $jurnal->update([
            'status_validasi' => $status,
            'catatan_validasi' => $request->catatan_validasi,
            'divalidasi_pada' => Carbon::now('Asia/Jakarta'),
        ]);

        $msg = $status === 'disetujui'
            ? 'Logbook berhasil disetujui!'
            : 'Logbook ditolak dan catatan revisi telah dikirim ke guru.';

        return redirect()
            ->route('pengurus-kelas.jurnal-detail', ['id' => $id])
            ->with('success', $msg);
    }

    /**
     * Status Kehadiran Guru Hari Ini
     */
    public function kehadiranGuru()
    {
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        $hariIni = $now->translatedFormat('l');
        $tanggalFormatted = $now->translatedFormat('l, d F Y');

        $kelas = $this->getKelasPengurus();
        $kelasId = $kelas?->id_kelas;

        $jadwals = collect();
        if ($kelasId) {
            $jadwals = JadwalMengajar::with(['user', 'mapel'])
                ->where('id_kelas', $kelasId)
                ->where('hari', $hariIni)
                ->orderBy('jam_mulai')
                ->get();
        }

        $jurnalHariIni = collect();
        if ($kelasId) {
            $jurnalHariIni = JurnalMengajar::where('id_kelas', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->get()
                ->keyBy('jam_ke');
        }

        return view('dashboard.pengurus-kelas.kehadiran-guru', compact(
            'kelas',
            'tanggalFormatted',
            'hariIni',
            'jadwals',
            'jurnalHariIni'
        ));
    }

    /**
     * Rekap / Presensi Siswa Kelas
     */
    public function kehadiranSiswa()
    {
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        $tanggalFormatted = $now->translatedFormat('l, d F Y');

        $kelas = $this->getKelasPengurus();
        $kelasId = $kelas?->id_kelas;

        $siswas = collect();
        if ($kelasId) {
            $siswas = Siswa::where('kelas_id', $kelasId)
                ->orderBy('nama')
                ->get();
        }

        // Ambil absensi dari jurnal terakhir hari ini jika ada
        $absensiTerakhir = collect();
        if ($kelasId) {
            $jurnalHariIni = JurnalMengajar::where('id_kelas', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->latest('id_jurnal')
                ->first();

            if ($jurnalHariIni) {
                $absensiTerakhir = Absensi::where('id_jurnal', $jurnalHariIni->id_jurnal)
                    ->pluck('status', 'id_siswa');
            }
        }

        return view('dashboard.pengurus-kelas.kehadiran-siswa', compact(
            'kelas',
            'tanggalFormatted',
            'siswas',
            'absensiTerakhir'
        ));
    }
}
