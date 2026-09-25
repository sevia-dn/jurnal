<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\JurnalMengajar;
use App\Models\Siswa;
use App\Services\DispensasiWorkflowService;
use App\Services\LogbookDeadlinePolicy;
use App\Services\ScheduleTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    /**
     * Menyimpan Jurnal Pembelajaran dan Rekap Absensi Siswa oleh Guru
     */
    public function store(
        Request $request,
        DispensasiWorkflowService $workflowService,
        LogbookDeadlinePolicy $deadlinePolicy,
        ScheduleTimeService $scheduleTimeService,
    ) {
        Carbon::setLocale('id');

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();

        // 1. Validasi input form jurnal, lampiran bukti hadir, dan absensi siswa
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mapels,id',
            'jam_ke' => 'required|integer|min:1|max:13',
            'jam_selesai' => 'nullable|integer|min:1|max:13|gte:jam_ke',
            'tanggal' => 'nullable|date',
            'materi' => 'required|string|max:500',
            'ada_tugas' => 'required|in:Ya,Tidak',
            'catatan' => 'nullable|string',
            'lampiran' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'absensi' => 'nullable|array',
            'absensi.*' => 'nullable|in:Hadir,Sakit,Izin,Alpa,D,Dispensasi',
            'absensi_catatan' => 'nullable|array',
            'absensi_catatan.*' => 'nullable|string|max:255',
        ], [
            'materi.required' => 'Materi / Pokok Pembahasan wajib diisi.',
            'jam_selesai.gte' => 'Jam selesai mengajar harus lebih besar atau sama dengan jam mulai.',
            'lampiran.required' => 'Lampiran foto atau berkas bukti kehadiran di kelas wajib diunggah.',
            'lampiran.file' => 'Lampiran harus berupa berkas/file yang valid.',
            'lampiran.mimes' => 'Format lampiran harus berupa foto (JPG, PNG, WebP) atau berkas PDF.',
            'lampiran.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
        ]);

        $jamMulai = (int) $request->jam_ke;
        $jamSelesai = (int) ($request->jam_selesai ?: $request->jam_ke);
        $journalDate = Carbon::parse($request->input('tanggal', $todayDate), 'Asia/Jakarta')->startOfDay();
        $journalDateString = $journalDate->toDateString();

        // 2. Terapkan kebijakan yang disimpan Admin pada tanggal jurnal yang dipilih.
        $hariJurnal = $journalDate->translatedFormat('l');
        $slotMulai = $scheduleTimeService->slot($hariJurnal, $jamMulai);
        $slotSelesai = $scheduleTimeService->slot($hariJurnal, $jamSelesai);
        $startSlot = $slotMulai['start'];
        $endSlot = $slotSelesai['end'];

        $violation = $deadlinePolicy->violation($now, $journalDate, $startSlot, $endSlot);
        if ($violation) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $violation);
        }

        // 3. Cek apakah guru sudah mengirimkan jurnal untuk kelas, mapel, tanggal, dan jam_ke yang sama
        $existing = JurnalMengajar::where('id_user', $user->id)
            ->where('id_kelas', $request->id_kelas)
            ->where('id_mapel', $request->id_mapel)
            ->whereDate('tanggal', $journalDateString)
            ->where('jam_ke', $jamMulai)
            ->exists();

        if ($existing) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Anda sudah pernah mengirimkan jurnal pembelajaran untuk kelas dan jam pelajaran ini pada tanggal tersebut.');
        }

        // 4. Upload lampiran bukti kehadiran guru di kelas (jika ada)
        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('jurnal-lampiran', 'public');
        }

        // 5. Hitung rekap absensi siswa di kelas yang dipilih
        // Ambil semua siswa yang terdaftar di kelas tersebut
        $daftarSiswaKelas = Siswa::where('kelas_id', $request->id_kelas)
            ->orderBy('id')
            ->get();
        $idSiswaKelas = $daftarSiswaKelas->pluck('id')->all();
        $inputAbsensi = collect($request->input('absensi', []))
            ->only($idSiswaKelas);
        $inputCatatanAbsensi = collect($request->input('absensi_catatan', []))
            ->only($idSiswaKelas);
        $dispensasis = Dispensasi::query()
            ->whereIn('siswa_id', $idSiswaKelas)
            ->where('status_akhir', 'disetujui')
            ->whereDate('tanggal', '<=', $journalDateString)
            ->whereDate('tanggal_selesai', '>=', $journalDateString)
            ->get()
            ->groupBy('siswa_id');

        $jmlHadir = 0;
        $jmlSakit = 0;
        $jmlIzin = 0;
        $jmlAlpa = 0;
        $jmlDispensasi = 0;

        $absensiFinal = [];
        foreach ($daftarSiswaKelas as $s) {
            // Default status adalah 'Hadir' jika tidak ditentukan
            $st = $inputAbsensi->get($s->id, 'Hadir');
            if ($st === 'Dispensasi') {
                $st = 'D';
            }
            if ($dispensasis->get($s->id, collect())->contains(
                fn (Dispensasi $dispensasi): bool => $workflowService->isActiveForStudentAt($dispensasi, $journalDateString, $jamMulai)
            )) {
                $st = 'D';
            }
            $absensiFinal[$s->id] = [
                'status' => $st,
                'catatan' => $st === 'Hadir' ? null : $inputCatatanAbsensi->get($s->id),
            ];

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
                case 'D':
                    $jmlDispensasi++;
                    break;
                default:
                    $jmlHadir++;
                    break;
            }
        }
        $jmlTidakHadir = $jmlSakit + $jmlIzin + $jmlAlpa + $jmlDispensasi;

        // 6. Database Transaction
        DB::beginTransaction();
        try {
            $jurnal = JurnalMengajar::create([
                'id_user' => $user->id,
                'id_kelas' => $request->id_kelas,
                'id_mapel' => $request->id_mapel,
                'tanggal' => $journalDateString,
                'jam_ke' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'materi' => $request->materi,
                'keterangan' => null,
                'jumlah_hadir' => $jmlHadir,
                'jumlah_sakit' => $jmlSakit,
                'jumlah_izin' => $jmlIzin,
                'jumlah_alpa' => $jmlAlpa,
                'jumlah_dispensasi' => $jmlDispensasi,
                'jumlah_tidak_hadir' => $jmlTidakHadir,
                'status_kehadiran_guru' => 'Hadir',
                'ada_tugas' => $request->ada_tugas === 'Ya',
                'catatan' => $request->catatan,
                'lampiran' => $lampiranPath,
                'status_validasi' => 'belum_divalidasi',
                'catatan_validasi' => null,
                'divalidasi_pada' => null,
            ]);

            // Simpan satu detail presensi untuk setiap siswa di kelas jurnal.
            $jurnal->absensis()->createMany(
                collect($absensiFinal)->map(fn (array $absensiSiswa, int $idSiswa): array => [
                    'id_siswa' => $idSiswa,
                    'status' => $absensiSiswa['status'],
                    'catatan' => $absensiSiswa['catatan'],
                ])->values()->all()
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
     * Menampilkan Halaman Riwayat & Rekap Jurnal Mengajar Guru
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
}
