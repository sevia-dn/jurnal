<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Dispensasi;
use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Notifikasi;
use App\Models\PiketKehadiranSiswa;
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

        // 1. Total Sesi Hari Ini & Jadwal Pembelajaran Hari Ini
        $totalSesi = 0;
        $jadwals = collect();
        if ($kelasId) {
            $jadwals = JadwalMengajar::with(['user', 'mapel', 'kelas'])
                ->where('id_kelas', $kelasId)
                ->where('hari', $hariIni)
                ->orderBy('jam_mulai')
                ->get();
            $totalSesi = $jadwals->count();
        }

        // Cek logbook yang sudah terisi untuk setiap sesi jadwal hari ini
        $jurnalHariIni = collect();
        if ($kelasId) {
            $jurnalHariIni = JurnalMengajar::where('id_kelas', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->get()
                ->keyBy('jam_ke');
        }

        // 2. Jurnal Perlu Persetujuan (belum_divalidasi)
        $perluPersetujuanQuery = JurnalMengajar::where('status_validasi', 'belum_divalidasi');
        if ($kelasId) {
            $perluPersetujuanQuery->where('id_kelas', $kelasId);
        }
        $perluPersetujuan = $perluPersetujuanQuery->count();

        // 3. Kehadiran Siswa — selalu hitung dari tabel siswa, bukan kolom jumlah_siswa
        $totalSiswa = $kelasId ? Siswa::where('kelas_id', $kelasId)->count() : 0;
        $siswaHadirCount = $totalSiswa;
        $siswaTidakHadirCount = 0;
        $jmlSakit = 0;
        $jmlIzin = 0;
        $jmlAlpa = 0;
        $jmlDispensasi = 0;

        if ($kelasId && $totalSiswa > 0) {
            $siswas = Siswa::where('kelas_id', $kelasId)->pluck('id');
            $jurnalHariIniIds = JurnalMengajar::where('id_kelas', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->pluck('id_jurnal');

            // Absensi siswa dari jurnal guru hari ini
            $absensiJurnal = collect();
            if ($jurnalHariIniIds->isNotEmpty()) {
                $priority = [
                    'D' => 5, 'DISPENSASI' => 5,
                    'SAKIT' => 4, 'S' => 4,
                    'IZIN' => 3, 'I' => 3,
                    'ALPA' => 2, 'ALFA' => 2, 'A' => 2,
                    'HADIR' => 1, 'H' => 1,
                ];
                $absensiJurnal = Absensi::whereIn('id_jurnal', $jurnalHariIniIds)
                    ->get()
                    ->groupBy('id_siswa')
                    ->map(fn ($records) => $records->sortByDesc(fn ($r) => $priority[strtoupper(trim((string) $r->status))] ?? 0)->first());
            }

            // Kehadiran siswa yang dicatat piket hari ini
            $piketKehadiranToday = PiketKehadiranSiswa::where('kelas_id', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->get()
                ->keyBy('siswa_id');

            // Dispensasi aktif & disetujui hari ini
            $dispensasiAktifHariIni = Dispensasi::whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelasId))
                ->whereDate('tanggal', '<=', $now->toDateString())
                ->whereDate('tanggal_selesai', '>=', $now->toDateString())
                ->where('status_akhir', 'disetujui')
                ->pluck('siswa_id')
                ->flip();

            foreach ($siswas as $sid) {
                if ($dispensasiAktifHariIni->has($sid)) {
                    $jmlDispensasi++;

                    continue;
                }

                $piketRec = $piketKehadiranToday->get($sid);
                $jurnalRec = $absensiJurnal->get($sid);

                // Ambil status terbaik (piket lebih prioritas)
                $status = null;
                if ($piketRec) {
                    $ps = strtoupper(trim((string) $piketRec->status));
                    if (in_array($ps, ['S', 'SAKIT', 'I', 'IZIN', 'A', 'ALPA', 'ALFA'], true)) {
                        $status = $ps;
                    }
                }

                if (! $status && $jurnalRec) {
                    $js = strtoupper(trim((string) $jurnalRec->status));
                    if (in_array($js, ['S', 'SAKIT', 'I', 'IZIN', 'A', 'ALPA', 'ALFA'], true)) {
                        $status = $js;
                    }
                }

                if ($status) {
                    if (in_array($status, ['S', 'SAKIT'], true)) {
                        $jmlSakit++;
                    } elseif (in_array($status, ['I', 'IZIN'], true)) {
                        $jmlIzin++;
                    } elseif (in_array($status, ['A', 'ALPA', 'ALFA'], true)) {
                        $jmlAlpa++;
                    }
                }
            }

            $siswaTidakHadirCount = $jmlSakit + $jmlIzin + $jmlAlpa + $jmlDispensasi;
            $siswaHadirCount = max(0, $totalSiswa - $siswaTidakHadirCount);
        }

        $kehadiranSiswaText = "{$siswaHadirCount}/{$totalSiswa}";

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
            'hariIni',
            'tanggalFormatted',
            'totalSesi',
            'perluPersetujuan',
            'kehadiranSiswaText',
            'siswaHadirCount',
            'siswaTidakHadirCount',
            'totalSiswa',
            'jmlSakit',
            'jmlIzin',
            'jmlAlpa',
            'jmlDispensasi',
            'jurnalAntrean',
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
        $filters = $request->validate([
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $tanggalMulai = $filters['tanggal_mulai'] ?? null;
        $tanggalSelesai = $filters['tanggal_selesai'] ?? null;
        $search = trim($filters['search'] ?? '');

        $query = JurnalMengajar::with(['user', 'mapel', 'kelas'])
            ->when($kelasId, fn ($q) => $q->where('id_kelas', $kelasId));

        $query->when($tanggalMulai, fn ($q) => $q->whereDate('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai, fn ($q) => $q->whereDate('tanggal', '<=', $tanggalSelesai))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($journalQuery) use ($search) {
                    $journalQuery
                        ->where('materi', 'like', "%{$search}%")
                        ->orWhere('catatan', 'like', "%{$search}%")
                        ->orWhere('tanggal', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('mapel', fn ($mapelQuery) => $mapelQuery->where('nama_mapel', 'like', "%{$search}%"));
                });
            });

        $jurnals = $query
            ->orderByDesc('tanggal')
            ->orderBy('jam_ke', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('dashboard.pengurus-kelas.riwayat', compact(
            'kelas',
            'jurnals',
            'tanggalMulai',
            'tanggalSelesai',
            'search'
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

        if ($jurnal->status_validasi !== 'belum_divalidasi') {
            return redirect()
                ->route('pengurus-kelas.jurnal-detail', ['id' => $jurnal->id_jurnal])
                ->with('error', 'Logbook ini sudah memiliki keputusan validasi dan tidak dapat divalidasi ulang.');
        }

        $status = $request->action === 'setujui' ? 'disetujui' : 'ditolak';
        $jurnal->update([
            'status_validasi' => $status,
            'catatan_validasi' => $request->catatan_validasi,
            'divalidasi_pada' => Carbon::now('Asia/Jakarta'),
        ]);

        if ($status === 'disetujui') {
            Notifikasi::create([
                'id_user' => $jurnal->id_user,
                'id_kelas' => null,
                'judul' => 'Logbook Disetujui',
                'pesan' => "Logbook {$jurnal->mapel?->nama_mapel} kelas {$jurnal->kelas?->nama_kelas} telah divalidasi Pengurus Kelas.",
                'tipe' => 'logbook_disetujui',
                'is_read' => false,
            ]);
        }

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

        // Ambil absensi dari SEMUA jurnal hari ini (bukan hanya terakhir)
        // dengan prioritas: D > S > I > A > Hadir, agar status non-hadir tidak tertimpa
        $absensiTerakhir = collect();
        if ($kelasId) {
            $jurnalHariIni = JurnalMengajar::where('id_kelas', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->pluck('id_jurnal');

            if ($jurnalHariIni->isNotEmpty()) {
                $priority = [
                    'D' => 5,
                    'DISPENSASI' => 5,
                    'SAKIT' => 4,
                    'S' => 4,
                    'IZIN' => 3,
                    'I' => 3,
                    'ALPA' => 2,
                    'ALFA' => 2,
                    'A' => 2,
                    'HADIR' => 1,
                    'H' => 1,
                ];
                $absensiTerakhir = Absensi::whereIn('id_jurnal', $jurnalHariIni)
                    ->get()
                    ->groupBy('id_siswa')
                    ->map(function ($records) use ($priority) {
                        // Ambil record dengan status prioritas tertinggi
                        return $records->sortByDesc(fn ($r) => $priority[strtoupper(trim((string) $r->status))] ?? 0)->first();
                    });
            }
        }

        // Overlay dengan data input kehadiran dari Guru Piket hari ini
        if ($kelasId) {
            $piketKehadiranToday = PiketKehadiranSiswa::where('kelas_id', $kelasId)
                ->whereDate('tanggal', $now->toDateString())
                ->get()
                ->keyBy('siswa_id');

            foreach ($piketKehadiranToday as $siswaId => $piketRec) {
                $existing = $absensiTerakhir->get($siswaId);
                $existingStatus = strtoupper(trim((string) ($existing?->status ?? '')));
                $isExistingNonHadir = in_array($existingStatus, ['S', 'SAKIT', 'I', 'IZIN', 'A', 'ALPA', 'ALFA', 'D', 'DISPENSASI'], true);
                $isPiketNonHadir = in_array(strtoupper(trim((string) $piketRec->status)), ['S', 'SAKIT', 'I', 'IZIN', 'A', 'ALPA', 'ALFA', 'D', 'DISPENSASI'], true);

                if ($isPiketNonHadir || ! $isExistingNonHadir) {
                    $absensiTerakhir->put($siswaId, (object) [
                        'status' => $piketRec->status,
                        'catatan' => $piketRec->catatan ? 'Dicatat Piket: '.$piketRec->catatan : 'Dicatat oleh Guru Piket',
                    ]);
                }
            }
        }

        // Overlay dengan dispensasi yang aktif & disetujui hari ini untuk kelas ini
        $dispensasiAktifHariIni = collect();
        if ($kelasId) {
            $dispensasiAktifHariIni = Dispensasi::with('siswa')
                ->whereHas('siswa', fn ($q) => $q->where('kelas_id', $kelasId))
                ->whereDate('tanggal', '<=', $now->toDateString())
                ->whereDate('tanggal_selesai', '>=', $now->toDateString())
                ->where('status_akhir', 'disetujui')
                ->get()
                ->keyBy('siswa_id');
        }

        return view('dashboard.pengurus-kelas.kehadiran-siswa', compact(
            'kelas',
            'tanggalFormatted',
            'siswas',
            'absensiTerakhir',
            'dispensasiAktifHariIni'
        ));
    }

    /**
     * Tandai semua notifikasi pengurus kelas sebagai sudah dibaca
     */
    public function markAllNotificationsRead()
    {
        $kelas = $this->getKelasPengurus();

        Notifikasi::where('id_user', Auth::id())
            ->when($kelas, fn ($q) => $q->orWhere('id_kelas', $kelas->id_kelas))
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    }
}
