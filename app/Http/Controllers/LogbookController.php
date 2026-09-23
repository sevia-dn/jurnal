<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\KehadiranGuru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\User;
use App\Services\ValidasiTenggatJurnalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    /**
     * Menyimpan Jurnal Pembelajaran dan Rekap Absensi Siswa oleh Guru
     */
    public function store(Request $request)
    {
        Carbon::setLocale('id');

        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $hariIni = $now->translatedFormat('l');

        // Tanggal jurnal (default hari ini, jika kebijakan longgar bisa menerima tanggal yang dikirim)
        $tanggal = $request->input('tanggal', $todayDate);

        $jamMulai = (int) $request->jam_ke;
        $jamSelesai = (int) ($request->jam_selesai ?: $request->jam_ke);

        // 1. Validasi Tenggat Pengisian Jurnal berdasarkan Kebijakan Aktif (REVISI 1)
        $validasiTenggat = ValidasiTenggatJurnalService::validasi($tanggal, $jamMulai, $jamSelesai, $hariIni);
        if (! $validasiTenggat['isValid']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $validasiTenggat['message']);
        }

        // 2. Validasi Form & Foto — WAJIB di setiap jurnal (bukti hadir tiap sesi mengajar)
        $fotoFile = $request->file('foto') ?? $request->file('lampiran');
        if (! $fotoFile) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Wajib mengambil/mengunggah foto bukti kehadiran di kelas untuk setiap jurnal yang diisi.');
        }

        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mapels,id',
            'tanggal' => 'nullable|date_format:Y-m-d',
            'jam_ke' => 'required|integer|min:1|max:13',
            'jam_selesai' => 'nullable|integer|min:1|max:13|gte:jam_ke',
            'materi' => 'required|string|max:500',
            'ada_tugas' => 'required|in:Ya,Tidak',
            'catatan' => 'nullable|string',
            'foto' => 'required|file|mimes:jpeg,png,jpg,webp|max:5120',
            'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'absensi' => 'nullable|array',
            'absensi.*' => 'nullable|in:Hadir,Sakit,Izin,Alpa',
        ], [
            'materi.required' => 'Materi / Pokok Pembahasan wajib diisi.',
            'jam_selesai.gte' => 'Jam selesai mengajar harus lebih besar atau sama dengan jam mulai.',
            'foto.mimes' => 'Format foto harus berupa gambar JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran berkas foto maksimal 5 MB.',
            'lampiran.mimes' => 'Format lampiran harus berupa foto (JPG, PNG, WebP) atau berkas PDF.',
            'lampiran.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
        ]);

        // 4. Cek duplikasi jurnal (kelas, mapel, tanggal, dan jam_ke yang sama)
        $existing = JurnalMengajar::where('id_user', $user->id)
            ->where('id_kelas', $request->id_kelas)
            ->where('id_mapel', $request->id_mapel)
            ->where('tanggal', $tanggal)
            ->where('jam_ke', $jamMulai)
            ->exists();

        if ($existing) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Anda sudah pernah mengirimkan jurnal pembelajaran untuk kelas dan jam pelajaran ini pada tanggal tersebut.');
        }

        // 5. Simpan file foto / lampiran
        $fotoPath = null;
        if ($fotoFile) {
            $fotoPath = $fotoFile->store('jurnal-foto', 'public');
        }

        // 6. Hitung rekap absensi siswa di kelas yang dipilih
        $daftarSiswaKelas = Siswa::where('kelas_id', $request->id_kelas)->get();
        $inputAbsensi = $request->input('absensi', []);

        $jmlHadir = 0;
        $jmlSakit = 0;
        $jmlIzin = 0;
        $jmlAlpa = 0;

        $absensiFinal = [];
        foreach ($daftarSiswaKelas as $s) {
            $st = $inputAbsensi[$s->id] ?? 'Hadir';
            $absensiFinal[$s->id] = $st;

            switch ($st) {
                case 'Sakit':
                    $jmlSakit++;
                    break;
                case 'Izin':
                    $jmlIzin++;
                    break;
                case 'Alpa':
                    $jmlAlpa++;
                    break;
                default:
                    $jmlHadir++;
                    break;
            }
        }
        $jmlTidakHadir = $jmlSakit + $jmlIzin + $jmlAlpa;

        // 7. Database Transaction
        DB::beginTransaction();
        try {
            $jurnal = JurnalMengajar::create([
                'id_user' => $user->id,
                'id_kelas' => $request->id_kelas,
                'id_mapel' => $request->id_mapel,
                'tanggal' => $tanggal,
                'jam_ke' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'materi' => $request->materi,
                'keterangan' => null,
                'jumlah_hadir' => $jmlHadir,
                'jumlah_sakit' => $jmlSakit,
                'jumlah_izin' => $jmlIzin,
                'jumlah_alpa' => $jmlAlpa,
                'jumlah_dispensasi' => 0,
                'jumlah_tidak_hadir' => $jmlTidakHadir,
                'status_kehadiran_guru' => 'Hadir',
                'ada_tugas' => $request->ada_tugas === 'Ya',
                'catatan' => $request->catatan,
                'foto' => $fotoPath,
                'lampiran' => $fotoPath,
                'status_validasi' => 'belum_divalidasi',
                'catatan_validasi' => null,
                'divalidasi_pada' => null,
            ]);

            // Simpan detail absensi tiap siswa di kelas
            foreach ($absensiFinal as $idSiswa => $statusSiswa) {
                Absensi::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_siswa' => $idSiswa,
                    'status' => $statusSiswa,
                ]);
            }

            // 8. Otomatis catat record kehadiran guru di tabel kehadiran_gurus (supaya piket tetap bisa pantau)
            KehadiranGuru::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'tanggal' => $tanggal,
                ],
                [
                    'jam_masuk' => $now->format('H:i:s'),
                    'status' => 'Hadir',
                ]
            );

            DB::commit();

            return redirect()
                ->route('guru.riwayat')
                ->with('success', 'Jurnal pembelajaran dan presensi siswa berhasil disimpan! Status: Menunggu Validasi Pengurus Kelas.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan logbook: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan Halaman Riwayat & Detail Jurnal Mengajar Guru
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $keyword = trim($request->query('keyword', ''));
        $filterStart = $request->query('start_date');
        $filterEnd = $request->query('end_date');

        $query = JurnalMengajar::with(['kelas', 'mapel', 'absensis.siswa'])
            ->where('id_user', $user->id);

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('materi', 'like', "%{$keyword}%")
                    ->orWhere('catatan', 'like', "%{$keyword}%")
                    ->orWhereHas('mapel', function ($m) use ($keyword) {
                        $m->where('nama_mapel', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('kelas', function ($k) use ($keyword) {
                        $k->where('nama_kelas', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($filterStart) {
            $query->whereDate('tanggal', '>=', $filterStart);
        }
        if ($filterEnd) {
            $query->whereDate('tanggal', '<=', $filterEnd);
        }

        $riwayatJurnals = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_ke', 'desc')
            ->get();

        // Notifikasi logbook yang telah disetujui pengurus kelas
        $notifikasiDisetujui = JurnalMengajar::with(['kelas', 'mapel'])
            ->where('id_user', $user->id)
            ->where('status_validasi', 'disetujui')
            ->orderBy('id_jurnal', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.guru-pengajar.riwayat', compact(
            'riwayatJurnals',
            'keyword',
            'filterStart',
            'filterEnd',
            'notifikasiDisetujui'
        ));
    }

    /**
     * Menampilkan Halaman Rekapitulasi Jurnal Mengajar Guru (REVISI 4)
     */
    public function rekap(Request $request)
    {
        Carbon::setLocale('id');
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $now = Carbon::now('Asia/Jakarta');
        $bulan = (int) $request->query('bulan', $now->month);
        $tahun = (int) $request->query('tahun', $now->year);
        $filterKelas = $request->query('id_kelas');
        $filterMapel = $request->query('id_mapel');

        // Tentukan target guru
        $targetUserId = $user->id;
        $isAdmin = in_array($user->role, ['admin', 'waka']);
        if ($isAdmin && $request->filled('guru_id')) {
            $targetUserId = (int) $request->query('guru_id');
        }
        $targetUser = User::find($targetUserId) ?? $user;

        $startDate = Carbon::createFromDate($tahun, $bulan, 1, 'Asia/Jakarta')->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1, 'Asia/Jakarta')->endOfMonth();
        $today = Carbon::today('Asia/Jakarta');

        // Ambil jadwal mengajar target guru
        $jadwalQuery = JadwalMengajar::with(['kelas', 'mapel'])
            ->where('id_user', $targetUserId);
        if ($filterKelas) {
            $jadwalQuery->where('id_kelas', $filterKelas);
        }
        if ($filterMapel) {
            $jadwalQuery->where('id_mapel', $filterMapel);
        }
        $jadwals = $jadwalQuery->get();
        $jadwalByDay = $jadwals->groupBy('hari');

        // Ambil jurnal yang sudah diisi dalam rentang bulan & tahun ini
        $jurnalQuery = JurnalMengajar::with(['kelas', 'mapel'])
            ->where('id_user', $targetUserId)
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);
        if ($filterKelas) {
            $jurnalQuery->where('id_kelas', $filterKelas);
        }
        if ($filterMapel) {
            $jurnalQuery->where('id_mapel', $filterMapel);
        }
        $jurnals = $jurnalQuery->orderBy('tanggal', 'desc')->orderBy('jam_ke', 'desc')->get();

        // Hari dalam bahasa Indonesia
        $hariMap = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
        ];

        // Buat daftar sesi & evaluasi keterisian
        $sessions = [];
        $totalTerjadwal = 0;
        $totalTerisi = $jurnals->count();

        // Batasi rentang evaluasi jadwal terjadwal sampai hari ini jika bulan berjalan
        $limitDate = $endDate->greaterThan($today) ? $today : $endDate;
        $cursor = $startDate->copy();

        while ($cursor->lte($limitDate)) {
            $dayEnglish = $cursor->format('l');
            if (isset($hariMap[$dayEnglish])) {
                $namaHari = $hariMap[$dayEnglish];
                $dateStr = $cursor->toDateString();
                $schedulesToday = $jadwalByDay->get($namaHari, collect());

                foreach ($schedulesToday as $sch) {
                    $totalTerjadwal++;

                    // Cari apakah ada jurnal yang cocok
                    $filled = $jurnals->first(function ($j) use ($dateStr, $sch) {
                        return $j->tanggal === $dateStr &&
                            $j->id_kelas == $sch->id_kelas &&
                            $j->id_mapel == $sch->id_mapel &&
                            $j->jam_ke == $sch->jam_mulai;
                    });

                    $sessions[] = [
                        'tanggal' => $dateStr,
                        'hari' => $namaHari,
                        'jam_ke' => $sch->jam_mulai.($sch->jam_selesai > $sch->jam_mulai ? ' - '.$sch->jam_selesai : ''),
                        'kelas' => $sch->kelas?->nama_kelas ?? '-',
                        'mapel' => $sch->mapel?->nama_mapel ?? '-',
                        'status' => $filled ? 'Terisi' : 'Kosong',
                        'jurnal' => $filled,
                    ];
                }
            }
            $cursor->addDay();
        }

        // Tambahkan juga jika ada jurnal yang diisi di luar jadwal resmi (misal jam tambahan/pengganti)
        foreach ($jurnals as $j) {
            $alreadyIncluded = collect($sessions)->contains(fn ($s) => $s['jurnal'] && $s['jurnal']->id_jurnal === $j->id_jurnal);
            if (! $alreadyIncluded) {
                $cDate = Carbon::parse($j->tanggal);
                $dEng = $cDate->format('l');
                $nHari = $hariMap[$dEng] ?? $cDate->translatedFormat('l');
                $sessions[] = [
                    'tanggal' => $j->tanggal,
                    'hari' => $nHari,
                    'jam_ke' => $j->jam_ke.($j->jam_selesai > $j->jam_ke ? ' - '.$j->jam_selesai : ''),
                    'kelas' => $j->kelas?->nama_kelas ?? '-',
                    'mapel' => $j->mapel?->nama_mapel ?? '-',
                    'status' => 'Terisi',
                    'jurnal' => $j,
                ];
            }
        }

        // Urutkan sesi: tanggal terbaru di atas, lalu jam_ke
        usort($sessions, function ($a, $b) {
            if ($a['tanggal'] === $b['tanggal']) {
                return strcmp($b['jam_ke'], $a['jam_ke']);
            }

            return strcmp($b['tanggal'], $a['tanggal']);
        });

        $totalKosong = collect($sessions)->where('status', 'Kosong')->count();
        $persentaseKepatuhan = $totalTerjadwal > 0
            ? round(($totalTerisi / $totalTerjadwal) * 100, 1)
            : ($totalTerisi > 0 ? 100 : 0);

        // Data filter
        $kelases = Kelas::orderBy('nama_kelas')->get();
        $mapels = Mapel::orderBy('nama_mapel')->get();
        $teachers = $isAdmin ? User::where('role', 'guru')->orderBy('name')->get() : collect();

        return view('dashboard.guru-pengajar.rekap', compact(
            'sessions',
            'totalTerjadwal',
            'totalTerisi',
            'totalKosong',
            'persentaseKepatuhan',
            'bulan',
            'tahun',
            'filterKelas',
            'filterMapel',
            'kelases',
            'mapels',
            'teachers',
            'targetUser',
            'targetUserId',
            'isAdmin'
        ));
    }
}
