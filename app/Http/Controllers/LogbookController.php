<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\KetidakhadiranGuru;
use App\Models\Notifikasi;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use App\Services\DispensasiWorkflowService;
use App\Services\LogbookDeadlinePolicy;
use App\Services\ScheduleTimeService;
use App\Services\StudentAttendanceSynchronizationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        StudentAttendanceSynchronizationService $synchronizationService,
    ) {
        Carbon::setLocale('id');

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $jamMulai = (int) $request->jam_ke;
        $jamSelesai = (int) ($request->jam_selesai ?: $request->jam_ke);
        $journalDate = Carbon::parse($request->input('tanggal', $todayDate), 'Asia/Jakarta')->startOfDay();
        $journalDateString = $journalDate->toDateString();

        $ketidakhadiranPending = KetidakhadiranGuru::where('user_id', $user->id)
            ->whereDate('tanggal', $journalDateString)
            ->where('status', 'pending')
            ->first();

        if ($ketidakhadiranPending) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Laporan ketidakhadiran Anda masih menunggu validasi dari Guru Piket. Anda baru dapat mengisi jurnal penugasan setelah pengajuan disetujui.');
        }

        $ketidakhadiranApproved = KetidakhadiranGuru::where('user_id', $user->id)
            ->whereDate('tanggal', $journalDateString)
            ->where('status', 'disetujui')
            ->first();

        $isGuruTidakHadir = $ketidakhadiranApproved !== null;
        $statusKehadiranGuru = $ketidakhadiranApproved ? $ketidakhadiranApproved->label_alasan : ($request->input('status_kehadiran_guru') ?: 'Hadir');

        // 1. Validasi input form jurnal, lampiran bukti hadir, dan absensi siswa
        if ($isGuruTidakHadir) {
            $request->validate([
                'id_kelas' => 'required|exists:kelas,id_kelas',
                'id_mapel' => 'required|exists:mapels,id',
                'jam_ke' => 'required|integer|min:1|max:13',
                'jam_selesai' => 'nullable|integer|min:1|max:13|gte:jam_ke',
                'tanggal' => 'nullable|date',
                'materi' => 'required|string|max:500',
                'catatan' => 'nullable|string',
                'lampiran' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx|max:10240',
            ], [
                'materi.required' => 'Materi / Rincian Penugasan Siswa wajib diisi.',
                'jam_selesai.gte' => 'Jam selesai mengajar harus lebih besar atau sama dengan jam mulai.',
                'lampiran.file' => 'Lampiran harus berupa berkas/file yang valid.',
                'lampiran.max' => 'Ukuran berkas lampiran maksimal 10 MB.',
            ]);
        } else {
            $request->validate([
                'id_kelas' => 'required|exists:kelas,id_kelas',
                'id_mapel' => 'required|exists:mapels,id',
                'jam_ke' => 'required|integer|min:1|max:13',
                'jam_selesai' => 'nullable|integer|min:1|max:13|gte:jam_ke',
                'tanggal' => 'nullable|date',
                'materi' => 'required|string|max:500',
                'ada_tugas' => 'required|in:Ya,Tidak',
                'catatan' => 'nullable|string',
                'lampiran' => $request->filled('lampiran_base64') ? 'nullable' : 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
                'lampiran_base64' => 'nullable|string',
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
        }

        // 2. Terapkan kebijakan yang disimpan Admin pada tanggal jurnal yang dipilih.
        $hariJurnal = $journalDate->translatedFormat('l');
        $slotMulai = $scheduleTimeService->slot($hariJurnal, $jamMulai);
        $slotSelesai = $scheduleTimeService->slot($hariJurnal, $jamSelesai);
        $startSlot = $slotMulai['start'];
        $endSlot = $slotSelesai['end'];
        $dismissalTime = $scheduleTimeService->dismissalTimeForDate($journalDateString);

        if (! $scheduleTimeService->isLessonRangeApplicableOnDate($journalDateString, $hariJurnal, $jamMulai, $jamSelesai)
            || ($dismissalTime !== null && $journalDateString === $todayDate && $now->format('H:i') >= $dismissalTime)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Jadwal sesi ini sudah tidak berlaku karena ada kegiatan atau pulang cepat pada tanggal tersebut.');
        }

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
        } elseif ($request->filled('lampiran_base64')) {
            $dataUri = (string) $request->input('lampiran_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUri, $matches)) {
                $imageType = strtolower($matches[1]);
                $imageData = substr($dataUri, strpos($dataUri, ',') + 1);
                $imageData = base64_decode($imageData);
                if ($imageData !== false) {
                    $ext = in_array($imageType, ['jpeg', 'jpg', 'png', 'webp'], true) ? ($imageType === 'jpeg' ? 'jpg' : $imageType) : 'jpg';
                    $filename = 'jurnal-lampiran/'.Str::random(40).'.'.$ext;
                    Storage::disk('public')->put($filename, $imageData);
                    $lampiranPath = $filename;
                }
            }
        }

        // 5. Hitung rekap absensi siswa di kelas yang dipilih (hanya jika guru hadir di kelas)
        $absensiFinal = [];
        $jmlHadir = 0;
        $jmlSakit = 0;
        $jmlIzin = 0;
        $jmlAlpa = 0;
        $jmlDispensasi = 0;
        $jmlTidakHadir = 0;

        if (! $isGuruTidakHadir) {
            $daftarSiswaKelas = Siswa::where('kelas_id', $request->id_kelas)
                ->orderBy('id')
                ->get();
            $idSiswaKelas = $daftarSiswaKelas->pluck('id')->all();
            $inputAbsensi = collect($request->input('absensi', []));
            $inputCatatanAbsensi = collect($request->input('absensi_catatan', []));
            $piketAttendances = PiketKehadiranSiswa::query()
                ->whereDate('tanggal', $journalDateString)
                ->whereIn('siswa_id', $idSiswaKelas)
                ->get()
                ->keyBy('siswa_id');
            $dispensasis = Dispensasi::query()
                ->whereIn('siswa_id', $idSiswaKelas)
                ->where('status_akhir', 'disetujui')
                ->whereDate('tanggal', '<=', $journalDateString)
                ->whereDate('tanggal_selesai', '>=', $journalDateString)
                ->get()
                ->groupBy('siswa_id');

            foreach ($daftarSiswaKelas as $s) {
                // Status absensi siswa dari input form (dukung key integer dan string)
                $rawStatus = $inputAbsensi->get($s->id) ?? $inputAbsensi->get((string) $s->id) ?? 'Hadir';
                $st = trim((string) $rawStatus);

                if (in_array(strtolower($st), ['d', 'dispensasi'], true)) {
                    $st = 'D';
                } elseif (in_array(strtolower($st), ['a', 'alpa', 'alfa'], true)) {
                    $st = 'Alpa';
                } elseif (in_array(strtolower($st), ['s', 'sakit'], true)) {
                    $st = 'Sakit';
                } elseif (in_array(strtolower($st), ['i', 'izin'], true)) {
                    $st = 'Izin';
                } else {
                    $st = 'Hadir';
                }

                $piketAttendance = $piketAttendances->get($s->id);
                $hasActiveDispensasi = $dispensasis->get($s->id, collect())->contains(
                    fn (Dispensasi $dispensasi): bool => $workflowService->isActiveForStudentAt($dispensasi, $journalDateString, $jamMulai)
                );
                if ($piketAttendance !== null && ($piketAttendance->sumber !== PiketKehadiranSiswa::SumberDispensasiWaka || $hasActiveDispensasi)) {
                    $st = $synchronizationService->normalizeStatus($piketAttendance->status);
                }

                if ($hasActiveDispensasi) {
                    $st = 'D';
                }

                $rawCatatan = $inputCatatanAbsensi->get($s->id) ?? $inputCatatanAbsensi->get((string) $s->id);
                $catatan = $piketAttendance !== null && ($piketAttendance->sumber !== PiketKehadiranSiswa::SumberDispensasiWaka || $hasActiveDispensasi)
                    ? $synchronizationService->automaticNote($piketAttendance)
                    : ($st === 'Hadir' ? null : (trim((string) $rawCatatan) ?: null));

                $absensiFinal[$s->id] = [
                    'status' => $st,
                    'catatan' => $catatan,
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
        }

        $isLate = $now->toDateString() > $journalDateString;
        $lateMode = $isLate ? ($deadlinePolicy->configuration()['mode'] ?? 'los') : null;

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
                'status_kehadiran_guru' => $isGuruTidakHadir ? $statusKehadiranGuru : 'Hadir',
                'ada_tugas' => $isGuruTidakHadir ? true : ($request->ada_tugas === 'Ya'),
                'catatan' => $request->catatan,
                'lampiran' => $lampiranPath,
                'status_validasi' => 'belum_divalidasi',
                'catatan_validasi' => null,
                'divalidasi_pada' => null,
                'filled_at' => $now,
                'is_late' => $isLate,
                'late_mode' => $lateMode,
            ]);

            // Simpan satu detail presensi untuk setiap siswa jika guru hadir
            if (! $isGuruTidakHadir && count($absensiFinal) > 0) {
                $jurnal->absensis()->createMany(
                    collect($absensiFinal)->map(fn (array $absensiSiswa, int $idSiswa): array => [
                        'id_siswa' => $idSiswa,
                        'status' => $absensiSiswa['status'],
                        'catatan' => $absensiSiswa['catatan'],
                    ])->values()->all()
                );
            }

            // Kirim notifikasi ke pengurus kelas bahwa jurnal baru telah dikirim dan butuh validasi
            $targetKelas = Kelas::find($request->id_kelas);
            if ($targetKelas) {
                $namaKelasTarget = $targetKelas->nama_kelas;
                $pengurusUsers = User::where('role', 'pengurus_kelas')
                    ->get()
                    ->filter(function ($u) use ($namaKelasTarget) {
                        $clean = trim(str_ireplace('Pengurus Kelas ', '', $u->name));

                        return $clean === $namaKelasTarget || $u->name === $namaKelasTarget;
                    });

                foreach ($pengurusUsers as $pengurus) {
                    $judulNotif = $isGuruTidakHadir ? 'Jurnal Penugasan Baru Menunggu Validasi' : 'Jurnal Baru Menunggu Validasi';
                    $pesanNotif = $isGuruTidakHadir
                        ? "Bpk/Ibu {$user->name} ({$statusKehadiranGuru}) baru saja mengirimkan materi & tugas untuk kelas {$namaKelasTarget} jam ke-{$jamMulai}. Silakan periksa dan validasi."
                        : "Bpk/Ibu {$user->name} baru saja mengirimkan jurnal mengajar kelas {$namaKelasTarget} jam ke-{$jamMulai}. Silakan periksa dan validasi.";

                    Notifikasi::create([
                        'id_user' => $pengurus->id,
                        'id_kelas' => $targetKelas->id_kelas,
                        'id_dispensasi' => null,
                        'id_jurnal' => $jurnal->id_jurnal,
                        'judul' => $judulNotif,
                        'pesan' => $pesanNotif,
                        'tipe' => 'jurnal_baru',
                        'is_read' => false,
                    ]);
                }
            }

            DB::commit();

            $successMessage = $isGuruTidakHadir
                ? 'Jurnal penugasan mandiri (Guru '.$statusKehadiranGuru.') berhasil disimpan! Status: Menunggu Validasi Pengurus Kelas.'
                : 'Jurnal pembelajaran dan presensi siswa berhasil disimpan! Status: Menunggu Validasi Pengurus Kelas.';

            return redirect()
                ->route('guru.riwayat')
                ->with('success', $successMessage);
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
        $keyword = trim((string) $request->query('keyword', $request->query('search', '')));
        $filterStart = $request->query('start_date');
        $filterEnd = $request->query('end_date');
        $filterStatus = $request->query('status_validasi', '');

        $query = JurnalMengajar::with(['kelas', 'mapel', 'absensis.siswa'])
            ->where('id_user', $user->id);

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('materi', 'like', "%{$keyword}%")
                    ->orWhere('catatan', 'like', "%{$keyword}%")
                    ->orWhere('tanggal', 'like', "%{$keyword}%")
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

        if ($filterStatus === 'disetujui') {
            $query->where('status_validasi', 'disetujui');
        } elseif ($filterStatus === 'menunggu') {
            $query->where(function ($q) {
                $q->whereNull('status_validasi')
                    ->orWhere('status_validasi', 'belum_divalidasi')
                    ->orWhere('status_validasi', '');
            });
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
            'filterStatus',
            'notifikasiDisetujui'
        ));
    }
}
