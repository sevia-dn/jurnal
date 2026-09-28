<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Dispensasi;
use App\Models\JadwalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\JurnalMengajar;
use App\Models\KehadiranGuru;
use App\Models\Kelas;
use App\Models\KetidakhadiranGuru;
use App\Models\Notifikasi;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use App\Services\PiketScheduleService;
use App\Services\WhatsAppService;
use App\SimplePdfDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PiketController extends Controller
{
    protected $whatsAppService;

    protected PiketScheduleService $piketScheduleService;

    public function __construct(WhatsAppService $whatsAppService, PiketScheduleService $piketScheduleService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->piketScheduleService = $piketScheduleService;
    }

    public function utama(Request $request)
    {
        if (! $this->canAccessPiket()) {
            return $this->notScheduledResponse();
        }

        $today = now('Asia/Jakarta')->toDateString();

        // Parameter filter logbook
        $filterDate = $request->query('tanggal');
        $filterStart = $request->query('tanggal_mulai');
        $filterEnd = $request->query('tanggal_selesai');
        $filterPreset = $request->query('preset', 'semua');
        $search = trim((string) $request->query('search', ''));
        $statusValidasi = $request->query('status_validasi');

        $query = JurnalMengajar::with(['guru', 'kelas', 'mapel']);

        if ($filterDate) {
            $query->whereDate('tanggal', $filterDate);
        } elseif ($filterStart || $filterEnd) {
            if ($filterStart) {
                $query->whereDate('tanggal', '>=', $filterStart);
            }
            if ($filterEnd) {
                $query->whereDate('tanggal', '<=', $filterEnd);
            }
        } elseif ($filterPreset === 'hari_ini') {
            $query->whereDate('tanggal', $today);
        } elseif ($filterPreset === '7_hari') {
            $startDate = now('Asia/Jakarta')->subDays(6)->toDateString();
            $query->whereBetween('tanggal', [$startDate, $today]);
        } elseif ($filterPreset === '30_hari') {
            $startDate = now('Asia/Jakarta')->subDays(29)->toDateString();
            $query->whereBetween('tanggal', [$startDate, $today]);
        }

        if ($statusValidasi && in_array($statusValidasi, ['disetujui', 'belum_divalidasi', 'ditolak'], true)) {
            $query->where('status_validasi', $statusValidasi);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('materi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%")
                    ->orWhereHas('guru', fn ($g) => $g->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('kelas', fn ($k) => $k->where('nama_kelas', 'like', "%{$search}%"))
                    ->orWhereHas('mapel', fn ($m) => $m->where('nama_mapel', 'like', "%{$search}%"));
            });
        }

        $journals = $query->orderBy('tanggal', 'desc')
            ->orderBy('jam_ke', 'desc')
            ->latest('id_jurnal')
            ->get();

        // Kehadiran Guru: dihitung berdasarkan target tanggal (default hari ini)
        $targetDate = $filterDate ?: $today;
        $journalsOnTarget = JurnalMengajar::whereDate('tanggal', $targetDate)->get();
        $submittedTeacherIds = $journalsOnTarget->pluck('id_user')->unique();

        $guruHadir = JurnalMengajar::with(['guru', 'kelas'])
            ->whereDate('tanggal', $targetDate)
            ->get()
            ->unique('id_user')
            ->map(fn ($j) => [
                'nama' => $j->guru?->name ?? 'Guru',
                'status' => 'Hadir',
                'keterangan' => 'Jurnal terisi',
                'kelas' => $j->kelas?->nama_kelas ?? '-',
            ])->values();

        $teacherAbsenceReports = KehadiranGuru::with('user')
            ->whereDate('tanggal', $targetDate)
            ->whereIn('status', ['Sakit', 'Izin'])
            ->get();

        $dispensasiCount = Dispensasi::count();
        $dispensasiPendingCount = Dispensasi::where(fn ($q) => $q->whereNull('status_waka')->orWhereIn('status_waka', ['menunggu', 'pending']))->count();

        return view('dashboard.piket.utama', [
            'journals' => $journals,
            'journalCount' => $journals->count(),
            'presentTeacherCount' => $submittedTeacherIds->count(),
            'validatedJournalCount' => $journals->where('status_validasi', 'disetujui')->count(),
            'pendingJournalCount' => $journals->where('status_validasi', 'belum_divalidasi')->count(),
            'sickTeacherCount' => $teacherAbsenceReports->where('status', 'Sakit')->count(),
            'permissionTeacherCount' => $teacherAbsenceReports->where('status', 'Izin')->count(),
            'guruHadir' => $guruHadir,
            'teacherAbsenceReports' => $teacherAbsenceReports,
            'dispensasiCount' => $dispensasiCount,
            'dispensasiPendingCount' => $dispensasiPendingCount,
            'today' => $today,
            'targetDate' => $targetDate,
            'filterDate' => $filterDate,
            'filterStart' => $filterStart,
            'filterEnd' => $filterEnd,
            'filterPreset' => $filterPreset,
            'search' => $search,
            'statusValidasi' => $statusValidasi,
        ]);
    }

    // Halaman Rekap Kehadiran Guru
    public function kehadiran(Request $request)
    {
        if (! $this->canAccessPiket()) {
            return $this->notScheduledResponse();
        }
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        // Ambil data kehadiran guru pada tanggal yang dipilih
        $kehadiranRecords = KehadiranGuru::whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('user_id');

        $journalTeacherIds = JurnalMengajar::query()
            ->whereDate('tanggal', $tanggal)
            ->pluck('id_user')
            ->unique();

        $teachersData = $gurus->map(function ($guru) use ($kehadiranRecords, $journalTeacherIds) {
            $record = $kehadiranRecords->get($guru->id);

            $status = in_array($record?->status, ['Sakit', 'Izin'], true) ? $record->status : null;
            if ($journalTeacherIds->contains($guru->id)) {
                $status = 'Hadir';
            }

            return [
                'user_id' => $guru->id,
                'id' => $record?->id,
                'name' => $guru->name,
                'nip' => $guru->nip ?? '-',
                'no_hp' => $guru->no_hp ?? '-',
                'checkIn' => $status === 'Hadir' ? 'Jurnal terisi' : ($status ? 'Laporan piket' : 'Belum ada catatan'),
                'status' => $status ?? 'Belum Hadir',
                'keterangan' => $record?->keterangan,
                'verified' => $record && $record->diverifikasi_at !== null,
            ];
        })->values();

        // Hitung statistik guru hari ini
        $totalGuru = $gurus->count();
        $totalHadir = $teachersData->where('status', 'Hadir')->count();
        $totalIzin = $teachersData->where('status', 'Izin')->count();
        $totalSakit = $teachersData->where('status', 'Sakit')->count();
        $totalBelumHadir = $teachersData->where('status', 'Belum Hadir')->count();

        return view('dashboard.piket.kehadiran', compact(
            'teachersData',
            'gurus',
            'tanggal',
            'totalGuru',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalBelumHadir'
        ));
    }

    // Halaman Form Lapor Kehadiran Guru (Izin / Sakit)
    public function laporKehadiranForm()
    {
        if (! $this->canAccessPiket()) {
            return $this->notScheduledResponse();
        }

        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        return view('dashboard.piket.lapor-kehadiran', compact('gurus'));
    }

    public function jurnalDetail(JurnalMengajar $jurnal)
    {
        $this->ensurePiketAccess();

        $jurnal->load(['guru', 'kelas', 'mapel', 'absensis.siswa']);

        return view('dashboard.piket.jurnal-detail', compact('jurnal'));
    }

    public function storeKehadiranGuru(Request $request)
    {
        $this->ensurePiketAccess();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:Sakit,Izin',
            'keterangan' => 'required|string|max:500',
        ]);

        $guru = User::query()->where('role', 'guru')->findOrFail($validated['user_id']);
        $today = now('Asia/Jakarta')->toDateString();

        if (JurnalMengajar::query()->where('id_user', $guru->id)->whereDate('tanggal', $today)->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Guru sudah tercatat hadir karena telah mengisi jurnal hari ini.');
        }

        KehadiranGuru::updateOrCreate(
            ['user_id' => $guru->id, 'tanggal' => $today],
            [
                'status' => $validated['status'],
                'keterangan' => $validated['keterangan'],
                'jam_masuk' => null,
                'diverifikasi_oleh' => auth()->id(),
                'diverifikasi_at' => now('Asia/Jakarta'),
            ],
        );

        // Kirim notifikasi ke semua pengurus kelas yang kelasnya diajar guru ini hari ini
        $hariIni = Carbon::now('Asia/Jakarta')->translatedFormat('l');
        $kelasIds = JadwalMengajar::where('id_user', $guru->id)
            ->where('hari', $hariIni)
            ->pluck('id_kelas')
            ->unique();

        $pengurusUsers = User::where('role', 'pengurus_kelas')->get();
        foreach ($pengurusUsers as $pengurus) {
            // Cari kelas pengurus ini
            $cleanName = trim(str_ireplace('Pengurus Kelas ', '', $pengurus->name));
            $kelasPengurus = Kelas::where('nama_kelas', $cleanName)
                ->orWhere('nama_kelas', $pengurus->name)
                ->first();

            if ($kelasPengurus && $kelasIds->contains($kelasPengurus->id_kelas)) {
                Notifikasi::create([
                    'id_user' => $pengurus->id,
                    'id_kelas' => $kelasPengurus->id_kelas,
                    'id_dispensasi' => null,
                    'judul' => 'Laporan Guru Tidak Hadir',
                    'pesan' => "Bpk/Ibu {$guru->name} dilaporkan {$validated['status']} hari ini oleh Petugas Piket. Keterangan: {$validated['keterangan']}",
                    'tipe' => 'guru_tidak_hadir',
                    'is_read' => false,
                ]);
            }
        }

        return redirect()
            ->route('piket.kehadiran')
            ->with('success', "Status {$validated['status']} untuk {$guru->name} berhasil dicatat.");
    }

    // Verifikasi kehadiran guru (dipanggil dari tombol "Verifikasi")
    public function verifikasiKehadiran(Request $request, $id)
    {
        $this->ensurePiketAccess();
        $kehadiran = KehadiranGuru::find($id);

        if (! $kehadiran) {
            // Jika belum ada record tapi piket ingin verifikasi hadir langsung
            $userId = $request->input('user_id');
            $tanggal = $request->input('tanggal', now()->format('Y-m-d'));

            $kehadiran = KehadiranGuru::create([
                'user_id' => $userId,
                'tanggal' => $tanggal,
                'jam_masuk' => now()->format('H:i:s'),
                'status' => 'Hadir',
                'diverifikasi_oleh' => auth()->id(),
                'diverifikasi_at' => now(),
            ]);
        } else {
            $kehadiran->update([
                'diverifikasi_oleh' => auth()->id(),
                'diverifikasi_at' => now(),
            ]);
        }

        return back()->with('success', 'Kehadiran guru berhasil diverifikasi.');
    }

    // Halaman Rekap Kehadiran Siswa
    public function kehadiranSiswa(Request $request)
    {
        if (! $this->canAccessPiket()) {
            return $this->notScheduledResponse();
        }
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        $selectedKelasId = $request->input('kelas_id', $kelasList->first()?->id_kelas);
        $selectedKelas = $kelasList->firstWhere('id_kelas', $selectedKelasId) ?? $kelasList->first();

        // Ambil siswa dari kelas yang dipilih
        $siswas = Siswa::where('kelas_id', $selectedKelas?->id_kelas)
            ->orderBy('nama')
            ->get();

        // Ambil dispensasi yang aktif & disetujui waka pada tanggal ini
        $dispensasis = Dispensasi::where('status_akhir', 'disetujui')
            ->whereDate('tanggal', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        // Catatan piket adalah sumber presensi harian lintas sesi guru.
        $kehadiranPiket = PiketKehadiranSiswa::query()
            ->where('kelas_id', $selectedKelas?->id_kelas)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        // Absensi jurnal tetap menjadi data per sesi pembelajaran (ambil status non-hadir prioritas tertinggi).
        $priorityMap = [
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
        $absensiRecords = Absensi::whereIn('id_siswa', $siswas->pluck('id'))
            ->whereHas('jurnal', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            })
            ->get()
            ->groupBy('id_siswa')
            ->map(fn ($records) => $records->sortByDesc(fn ($r) => $priorityMap[strtoupper(trim((string) $r->status))] ?? 0)->first());

        $studentsData = $siswas->map(function ($s) use ($dispensasis, $kehadiranPiket, $absensiRecords, $selectedKelas) {
            $dispen = $dispensasis->get($s->id);
            $piketRecord = $kehadiranPiket->get($s->id);
            $absen = $absensiRecords->get($s->id);

            $isPiketNonHadir = $piketRecord && in_array(strtoupper(trim((string) $piketRecord->status)), ['S', 'SAKIT', 'I', 'IZIN', 'A', 'ALPA', 'ALFA', 'D', 'DISPENSASI'], true);
            $isAbsenNonHadir = $absen && in_array(strtoupper(trim((string) $absen->status)), ['S', 'SAKIT', 'I', 'IZIN', 'A', 'ALPA', 'ALFA', 'D', 'DISPENSASI'], true);

            if ($dispen) {
                $status = 'D';
                $catatan = 'Dispensasi: '.$dispen->deskripsi_waktu.' ('.$dispen->alasan.')';
            } elseif ($isPiketNonHadir) {
                $status = $piketRecord->status;
                $catatan = $piketRecord->catatan ?? '-';
            } elseif ($isAbsenNonHadir) {
                $status = $absen->status;
                $catatan = $absen->catatan ?? '-';
            } elseif ($piketRecord) {
                $status = $piketRecord->status;
                $catatan = $piketRecord->catatan ?? '-';
            } elseif ($absen) {
                $status = $absen->status;
                $catatan = $absen->catatan ?? '-';
            } else {
                $status = 'Hadir';
                $catatan = '-';
            }

            return [
                'id' => $s->id,
                'nis' => $s->nis ?? $s->nisn,
                'nisn' => $s->nisn ?? $s->nis,
                'name' => $s->nama,
                'gender' => $s->jenis_kelamin,
                'class' => $selectedKelas?->nama_kelas ?? 'Umum',
                'status' => $status,
                'note' => $catatan,
                'is_dispen' => $dispen !== null,
                'is_piket_record' => $piketRecord !== null,
            ];
        });

        // Hitung statistik
        $totalSiswa = $studentsData->count();
        $totalHadir = $studentsData->where('status', 'Hadir')->count();
        $totalSakit = $studentsData->where('status', 'Sakit')->count();
        $totalIzin = $studentsData->where('status', 'Izin')->count();
        $totalAlfa = $studentsData->whereIn('status', ['Alfa', 'Alpa'])->count();
        $totalDispen = $studentsData->where('status', 'D')->count();
        $isEditableDate = $tanggal === now('Asia/Jakarta')->toDateString();

        return view('dashboard.piket.kehadiran-siswa', compact(
            'kelasList',
            'selectedKelas',
            'selectedKelasId',
            'tanggal',
            'studentsData',
            'totalSiswa',
            'totalHadir',
            'totalSakit',
            'totalIzin',
            'totalAlfa',
            'totalDispen',
            'isEditableDate'
        ));
    }

    // Update Status Kehadiran Siswa oleh Guru Piket
    public function updateKehadiranSiswa(Request $request)
    {
        $this->ensurePiketAccess();

        if ($request->boolean('bulk_attendance')) {
            return $this->updateBulkKehadiranSiswa($request);
        }

        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'status' => 'required|in:Hadir,Sakit,Izin,Alfa,D',
            'catatan' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'kelas_id' => 'required|exists:kelas,id_kelas',
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);

        if ((int) $siswa->kelas_id !== (int) $request->kelas_id) {
            abort(422, 'Siswa tidak terdaftar pada kelas yang dipilih.');
        }

        $today = now('Asia/Jakarta')->toDateString();
        if ($request->tanggal !== $today) {
            return back()->with('error', 'Status kehadiran hanya dapat diubah untuk tanggal hari ini. Tanggal lain hanya untuk pemantauan.');
        }

        $status = $request->status === 'Alfa' ? 'Alpa' : $request->status;
        PiketKehadiranSiswa::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tanggal' => $request->tanggal],
            [
                'kelas_id' => $siswa->kelas_id,
                'status' => $status,
                'catatan' => $request->catatan,
                'dicatat_oleh' => auth()->id(),
            ],
        );

        $this->notifyStudentAttendanceChange($siswa, $request->tanggal, $status, $request->catatan);

        return back()->with('success', "Status kehadiran untuk {$siswa->nama} berhasil diperbarui.");
    }

    private function updateBulkKehadiranSiswa(Request $request)
    {
        $validated = $request->validate([
            'absensi' => ['nullable', 'array'],
            'absensi.*' => ['required', 'in:Hadir,Sakit,Izin,Alfa,D'],
            'absensi_catatan' => ['nullable', 'array'],
            'absensi_catatan.*' => ['nullable', 'string', 'max:255'],
            'tanggal' => ['required', 'date'],
            'kelas_id' => ['required', 'exists:kelas,id_kelas'],
        ]);

        $today = now('Asia/Jakarta')->toDateString();
        if ($validated['tanggal'] !== $today) {
            return back()->with('error', 'Status kehadiran hanya dapat diubah untuk tanggal hari ini. Tanggal lain hanya untuk pemantauan.');
        }

        $attendance = $validated['absensi'] ?? [];
        if ($attendance === []) {
            return back()->with('success', 'Tidak ada perubahan presensi untuk disimpan.');
        }

        $studentIds = array_map('intval', array_keys($attendance));
        $students = Siswa::query()
            ->where('kelas_id', $validated['kelas_id'])
            ->whereKey($studentIds)
            ->with('kelas')
            ->get()
            ->keyBy('id');

        if ($students->count() !== count($studentIds)) {
            abort(422, 'Terdapat siswa yang tidak terdaftar pada kelas yang dipilih.');
        }

        $approvedDispensationIds = Dispensasi::query()
            ->where('status_akhir', 'disetujui')
            ->whereDate('tanggal', '<=', $validated['tanggal'])
            ->whereDate('tanggal_selesai', '>=', $validated['tanggal'])
            ->whereIn('siswa_id', $studentIds)
            ->pluck('siswa_id')
            ->flip();
        $existingRecords = PiketKehadiranSiswa::query()
            ->whereDate('tanggal', $validated['tanggal'])
            ->whereIn('siswa_id', $studentIds)
            ->get()
            ->keyBy('siswa_id');
        $changedAttendances = [];

        DB::transaction(function () use ($attendance, $validated, $students, $approvedDispensationIds, $existingRecords, &$changedAttendances): void {
            foreach ($attendance as $studentId => $requestedStatus) {
                if ($approvedDispensationIds->has($studentId)) {
                    continue;
                }

                $student = $students->get((int) $studentId);
                $status = $requestedStatus === 'Alfa' ? 'Alpa' : $requestedStatus;
                $note = trim((string) ($validated['absensi_catatan'][$studentId] ?? '')) ?: null;
                $existingRecord = $existingRecords->get((int) $studentId);

                if ($existingRecord?->status === $status && $existingRecord?->catatan === $note) {
                    continue;
                }

                PiketKehadiranSiswa::updateOrCreate(
                    ['siswa_id' => $student->id, 'tanggal' => $validated['tanggal']],
                    [
                        'kelas_id' => $student->kelas_id,
                        'status' => $status,
                        'catatan' => $note,
                        'dicatat_oleh' => auth()->id(),
                    ],
                );

                $changedAttendances[] = [$student, $status, $note];
            }
        });

        foreach ($changedAttendances as [$student, $status, $note]) {
            $this->notifyStudentAttendanceChange($student, $validated['tanggal'], $status, $note);
        }

        return back()->with('success', count($changedAttendances).' perubahan presensi siswa berhasil disimpan.');
    }

    private function notifyStudentAttendanceChange(Siswa $siswa, string $tanggal, string $status, ?string $catatan): void
    {
        $kelas = $siswa->kelas;
        if (! $kelas) {
            return;
        }

        $hari = Carbon::parse($tanggal, 'Asia/Jakarta')->locale('id')->translatedFormat('l');
        $teacherIds = JadwalMengajar::query()
            ->where('id_kelas', $kelas->id_kelas)
            ->where('hari', $hari)
            ->pluck('id_user')
            ->unique();
        $pengurus = User::query()
            ->where('role', 'pengurus_kelas')
            ->get()
            ->filter(fn (User $user) => trim(str_ireplace('Pengurus Kelas ', '', $user->name)) === $kelas->nama_kelas);
        $recipientIds = $teacherIds->merge($pengurus->pluck('id'))->unique();
        $tanggalLabel = Carbon::parse($tanggal, 'Asia/Jakarta')->translatedFormat('d F Y');
        $pesan = "{$siswa->nama} kelas {$kelas->nama_kelas} tercatat {$status} pada {$tanggalLabel}.";
        if ($catatan) {
            $pesan .= " Keterangan: {$catatan}";
        }

        foreach ($recipientIds as $recipientId) {
            Notifikasi::create([
                'id_user' => $recipientId,
                'id_kelas' => $kelas->id_kelas,
                'judul' => 'Pembaruan Kehadiran Siswa',
                'pesan' => $pesan,
                'tipe' => 'kehadiran_siswa_piket',
                'is_read' => false,
            ]);
        }
    }

    // Halaman form Pengajuan & Monitoring Dispensasi
    public function dispensasiForm()
    {
        if (! $this->canAccessPiket()) {
            return $this->notScheduledResponse();
        }

        $siswas = Siswa::with('kelas')->orderBy('nama')->get();
        // Ambil data jam pelajaran unik per jam_ke dari jadwal_pelajarans
        $daftarJam = JadwalPelajaran::select('jam_ke', 'jam_mulai', 'jam_selesai')
            ->orderBy('jam_ke')
            ->get()
            ->unique('jam_ke');

        return view('dashboard.piket.dispensasi', compact('siswas', 'daftarJam'));
    }

    // Simpan Pengajuan Dispensasi oleh Guru Piket
    public function dispensasiStore(Request $request)
    {
        $this->ensurePiketAccess();
        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswas,id',
            'jenis_dispensasi' => 'required|string',
            'mode_waktu' => 'required|in:sepanjang_hari,jam_tertentu',
            'jam_ke_mulai' => 'nullable|required_if:mode_waktu,jam_tertentu|integer|min:1',
            'jam_ke_selesai' => 'nullable|integer|gte:jam_ke_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
        ], [
            'siswa_ids.required' => 'Pilih setidaknya satu siswa.',
            'jam_ke_mulai.required_if' => 'Jam ke mulai wajib dipilih jika memilih mode Jam Tertentu.',
            'jam_ke_selesai.gte' => 'Jam ke selesai harus sama atau lebih besar dari jam ke mulai.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $siswaIds = $request->siswa_ids;
        // Siswa pertama sebagai representasi utama (backward compat)
        $siswaUtama = Siswa::with('kelas')->findOrFail($siswaIds[0]);

        // Tentukan jam dan hitung tipe_dispensasi secara otomatis di server
        $tglMulai = $request->tanggal_mulai;
        $tglSelesai = $request->tanggal_selesai;
        $modeWaktu = $request->mode_waktu;

        $jamKeMulai = ($modeWaktu === 'jam_tertentu') ? $request->jam_ke_mulai : null;
        $jamKeSelesai = ($modeWaktu === 'jam_tertentu' && ! empty($request->jam_ke_selesai)) ? $request->jam_ke_selesai : null;

        $isSingleDay = ($tglMulai === $tglSelesai);
        $hasJamMulai = ! empty($jamKeMulai);

        if ($isSingleDay && ! $hasJamMulai) {
            $tipeDispensasi = 'satu_hari';
        } elseif ($isSingleDay && $hasJamMulai) {
            $tipeDispensasi = 'per_jam';
        } elseif (! $isSingleDay && ! $hasJamMulai) {
            $tipeDispensasi = 'multi_hari_penuh';
        } else {
            $tipeDispensasi = 'multi_hari_per_jam';
        }

        $tokenApproval = Str::random(32);

        $dispensasi = Dispensasi::create([
            'siswa_id' => $siswaUtama->id,
            'jenis_dispensasi' => $request->jenis_dispensasi,
            'tipe_dispensasi' => $tipeDispensasi,
            'jam_ke_mulai' => $jamKeMulai,
            'jam_ke_selesai' => $jamKeSelesai,
            'tanggal' => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'alasan' => $request->alasan,
            'status_piket' => 'disetujui',
            'status_waka' => 'menunggu',
            'status_akhir' => 'menunggu',
            'token_approval' => $tokenApproval,
            'token_verifikasi' => Str::random(40),
            'dibuat_oleh' => auth()->id(),
        ]);

        // Simpan semua siswa ke pivot table dispensasi_siswa
        $dispensasi->siswas()->sync($siswaIds);

        $dispensasi->load(['siswa.kelas', 'pembuat']);

        $this->createPendingDispensasiNotifications($dispensasi);
        $whatsAppDelivery = $this->whatsAppService->sendDispensasiNotificationToWaka($dispensasi);

        $approvalUrl = route('dispensasi.approval', ['token' => $tokenApproval]);

        $jumlahSiswa = count($siswaIds);
        $namaUtama = $siswaUtama->nama;
        $message = $jumlahSiswa > 1
            ? "Pengajuan dispensasi untuk {$namaUtama} dan ".($jumlahSiswa - 1).' siswa lainnya berhasil dibuat.'
            : "Pengajuan dispensasi untuk {$namaUtama} berhasil dibuat.";

        if (! $whatsAppDelivery['configured']) {
            $message .= ' WhatsApp belum dikirim karena gateway belum dikonfigurasi.';
        } elseif ($whatsAppDelivery['delivered'] === $whatsAppDelivery['recipients']) {
            $message .= " WhatsApp berhasil dikirim ke {$whatsAppDelivery['delivered']} nomor tujuan.";
        } else {
            $message .= " Gateway WhatsApp hanya menerima {$whatsAppDelivery['delivered']} dari {$whatsAppDelivery['recipients']} nomor tujuan. Periksa log gateway.";
        }

        return redirect()->route('piket.dispensasi.form')
            ->with('success', $message)
            ->with('approval_url', $approvalUrl)
            ->with('token_approval', $tokenApproval);
    }

    public function dispensasiHistory(Request $request)
    {
        if (! $this->canAccessDispensasiHistory()) {
            return $this->notScheduledResponse();
        }

        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');
        $startDate = $request->query('tanggal_mulai');
        $endDate = $request->query('tanggal_selesai');

        $query = Dispensasi::with(['siswa.kelas', 'pembuat']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('alasan', 'like', "%{$search}%")
                    ->orWhere('jenis_dispensasi', 'like', "%{$search}%")
                    ->orWhereHas('siswa', function ($sq) use ($search) {
                        $sq->where('nama', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    })
                    ->orWhereHas('pembuat', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status && $status !== 'all') {
            if ($status === 'disetujui') {
                $query->whereIn('status_waka', ['disetujui', 'approved']);
            } elseif ($status === 'menunggu') {
                $query->where(function ($sq) {
                    $sq->whereNull('status_waka')
                        ->orWhereIn('status_waka', ['menunggu', 'pending']);
                });
            } elseif ($status === 'ditolak') {
                $query->whereIn('status_waka', ['ditolak', 'rejected']);
            }
        }

        if ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
        }

        $dispensasis = $query->latest('id')->paginate(15)->withQueryString();

        return view('dashboard.piket.dispensasi-history', compact(
            'dispensasis',
            'search',
            'status',
            'startDate',
            'endDate'
        ));
    }

    private function notScheduledResponse()
    {
        $user = Auth::user();
        $upcomingSchedules = $user
            ? $user->jadwalPikets()
                ->whereDate('tanggal', '>=', now('Asia/Jakarta')->toDateString())
                ->orderBy('tanggal')
                ->take(5)
                ->get()
            : collect();

        return view('dashboard.piket.not-scheduled', compact('upcomingSchedules'));
    }

    private function createPendingDispensasiNotifications(Dispensasi $dispensasi): void
    {
        $dispensasi->loadMissing(['siswa.kelas', 'pembuat']);
        $namaKelas = $dispensasi->siswa?->kelas?->nama_kelas ?? '-';
        $message = "Pengajuan dispensasi {$dispensasi->siswa?->nama} kelas {$namaKelas} dari "
            .($dispensasi->pembuat?->name ?? 'Guru Piket').'. Menunggu validasi Wakasek Kesiswaan.';

        User::wakaKesiswaan()->each(function (User $waka) use ($dispensasi, $message): void {
            Notifikasi::updateOrCreate(
                [
                    'id_user' => $waka->id,
                    'id_dispensasi' => $dispensasi->id,
                    'tipe' => 'dispensasi_menunggu',
                ],
                [
                    'id_kelas' => $dispensasi->siswa?->kelas_id,
                    'judul' => 'Pengajuan dispensasi menunggu validasi',
                    'pesan' => $message,
                    'is_read' => false,
                ],
            );
        });
    }

    private function canAccessPiket(): bool
    {
        $user = Auth::user();

        return $user !== null && (
            $user->role === 'admin'
            || $this->piketScheduleService->isScheduledNow($user)
        );
    }

    private function canAccessDispensasiHistory(): bool
    {
        $user = Auth::user();

        return $this->canAccessPiket()
            || $user?->isWaka()
            || ($user !== null && Notifikasi::where('id_user', $user->id)->whereNotNull('id_dispensasi')->exists());
    }

    private function ensurePiketAccess(): void
    {
        abort_unless($this->canAccessPiket(), 403, 'Akses Guru Piket hanya untuk petugas yang dijadwalkan hari ini.');
    }

    // =============================================
    // KETIDAKHADIRAN GURU (Feature 3)
    // =============================================

    /**
     * Daftar pengajuan ketidakhadiran guru yang masuk ke piket.
     */
    public function ketidakhadiranGuruIndex(Request $request)
    {
        $this->ensurePiketAccess();

        $status = $request->query('status', 'pending');
        $tanggal = $request->query('tanggal', now('Asia/Jakarta')->toDateString());

        $query = KetidakhadiranGuru::with('guru')
            ->where('tanggal', $tanggal);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $ketidakhadirans = $query->orderByDesc('created_at')->get();

        $pendingCount = KetidakhadiranGuru::where('tanggal', $tanggal)->where('status', 'pending')->count();

        return view('dashboard.piket.ketidakhadiran-guru', compact(
            'ketidakhadirans',
            'tanggal',
            'status',
            'pendingCount',
        ));
    }

    /**
     * Setujui pengajuan ketidakhadiran guru.
     */
    public function ketidakhadiranGuruApprove(Request $request, KetidakhadiranGuru $ketidakhadiran)
    {
        $this->ensurePiketAccess();

        $ketidakhadiran->update([
            'status' => 'disetujui',
            'handled_by' => auth()->id(),
            'handled_at' => now(),
            'catatan_piket' => $request->input('catatan_piket'),
        ]);

        return back()->with('success', "Ketidakhadiran {$ketidakhadiran->guru->name} pada {$ketidakhadiran->tanggal->translatedFormat('d F Y')} telah disetujui.");
    }

    /**
     * Tolak pengajuan ketidakhadiran guru.
     */
    public function ketidakhadiranGuruReject(Request $request, KetidakhadiranGuru $ketidakhadiran)
    {
        $this->ensurePiketAccess();

        $ketidakhadiran->update([
            'status' => 'ditolak',
            'handled_by' => auth()->id(),
            'handled_at' => now(),
            'catatan_piket' => $request->input('catatan_piket'),
        ]);

        return back()->with('success', "Pengajuan ketidakhadiran {$ketidakhadiran->guru->name} telah ditolak.");
    }

    // =============================================
    // REKAP JURNAL (Feature 5)
    // =============================================

    /**
     * Halaman rekap jurnal untuk Guru Piket (sama dengan Admin).
     */
    public function rekapJurnal(Request $request)
    {
        $this->ensurePiketAccess();

        $periode = $request->query('periode', 'harian');
        $tanggal = $request->query('tanggal', now('Asia/Jakarta')->toDateString());
        $bulan = min(12, max(1, (int) $request->query('bulan', now()->month)));
        $tahun = min(2100, max(2026, (int) $request->query('tahun', max(2026, now()->year))));
        $guruId = $request->query('guru_id');
        $kelasId = $request->query('kelas_id');
        $kehadiran = $request->query('kehadiran', 'all');
        $validasi = $request->query('validasi', 'all');
        $search = $request->query('search', '');
        $keterlambatan = $request->query('keterlambatan', 'all');
        $tab = $request->query('tab', 'jurnal');

        if ($request->has('bulan') && ! $request->has('tanggal')) {
            $periode = 'bulanan';
        }

        $daysCount = ['Senin' => 0, 'Selasa' => 0, 'Rabu' => 0, 'Kamis' => 0, 'Jumat' => 0, 'Sabtu' => 0];
        try {
            $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
                $h = match ($d->dayOfWeek) {
                    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', default => null
                };
                if ($h && isset($daysCount[$h])) {
                    $daysCount[$h]++;
                }
            }
        } catch (\Exception $e) {
            $startOfMonth = now()->startOfMonth();
            $endOfMonth = now()->endOfMonth();
        }

        try {
            $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;
        } catch (\Exception $e) {
            $tanggal = now()->toDateString();
            $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;
        }

        $namaHari = match ($dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };

        $query = JurnalMengajar::with(['kelas', 'guru', 'mapel']);

        $hasExplicitTanggal = $request->filled('tanggal');
        $hasSearch = $request->filled('search');

        if ($periode === 'bulanan') {
            $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        } elseif ($hasExplicitTanggal) {
            $query->whereDate('tanggal', $tanggal);
        } elseif (! $hasSearch) {
            $query->whereDate('tanggal', $tanggal);
        }

        if ($guruId && $guruId !== 'all') {
            $query->where('id_user', $guruId);
        }

        if ($kelasId && $kelasId !== 'all') {
            $query->where('id_kelas', $kelasId);
        }

        if ($keterlambatan === 'terlambat') {
            $query->where('menit_keterlambatan', '>', 0);
        } elseif ($keterlambatan === 'tepat_waktu') {
            $query->where('menit_keterlambatan', '<=', 0);
        }

        if ($request->filled('search')) {
            $term = trim($request->query('search'));
            $query->where(function ($q) use ($term) {
                $q->where('materi', 'like', "%{$term}%")
                    ->orWhere('tanggal', 'like', "%{$term}%")
                    ->orWhereHas('guru', fn ($g) => $g->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('mapel', fn ($m) => $m->where('nama_mapel', 'like', "%{$term}%"))
                    ->orWhereHas('kelas', fn ($k) => $k->where('nama_kelas', 'like', "%{$term}%"));
            });
        }

        $allJurnals = (clone $query)->orderBy('tanggal', 'desc')->orderBy('jam_ke', 'asc')->get();
        $jurnals = $allJurnals;

        $kelases = Kelas::orderBy('nama_kelas', 'asc')->get();
        $totalKelas = $kelases->count();
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        $jadwalQuery = JadwalPelajaran::query();
        if ($guruId && $guruId !== 'all') {
            $jadwalQuery->where('id_user', $guruId);
        }
        if ($kelasId && $kelasId !== 'all') {
            $jadwalQuery->where('id_kelas', $kelasId);
        }

        if ($periode === 'bulanan') {
            $jadwalsPerHari = $jadwalQuery->selectRaw('hari, count(*) as count')->groupBy('hari')->pluck('count', 'hari');
            $totalTerjadwal = 0;
            foreach ($daysCount as $h => $c) {
                $totalTerjadwal += $c * ($jadwalsPerHari[$h] ?? 0);
            }
            $kelasLapor = JurnalMengajar::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->distinct('id_kelas')->count('id_kelas');
            $guruTerlambat = JurnalMengajar::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->where('menit_keterlambatan', '>', 0)->count();
        } else {
            $totalTerjadwal = $namaHari ? $jadwalQuery->where('hari', $namaHari)->count() : 0;
            $kelasLapor = JurnalMengajar::whereDate('tanggal', $tanggal)->distinct('id_kelas')->count('id_kelas');
            $guruTerlambat = JurnalMengajar::whereDate('tanggal', $tanggal)->where('menit_keterlambatan', '>', 0)->count();
        }

        $totalTerisi = $allJurnals->count();
        $totalKosong = max(0, $totalTerjadwal - $totalTerisi);
        $guruHadir = $totalTerisi;
        $guruTidakHadir = 0;
        $guruAbsen = 0;

        $jadwalGuruGrouped = JadwalPelajaran::selectRaw('id_user, hari, count(*) as count')
            ->groupBy('id_user', 'hari')
            ->get()
            ->groupBy('id_user');

        if ($periode === 'bulanan') {
            $jurnalGuruGrouped = JurnalMengajar::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->selectRaw('id_user, count(*) as count, max(tanggal) as terakhir')
                ->groupBy('id_user')
                ->get()
                ->keyBy('id_user');
        } else {
            $jurnalGuruGrouped = JurnalMengajar::whereDate('tanggal', $tanggal)
                ->selectRaw('id_user, count(*) as count, max(tanggal) as terakhir')
                ->groupBy('id_user')
                ->get()
                ->keyBy('id_user');
        }

        $rekapGuru = $gurus->map(function ($g) use ($jadwalGuruGrouped, $jurnalGuruGrouped, $periode, $daysCount, $namaHari) {
            $jadwals = $jadwalGuruGrouped->get($g->id, collect());
            $terjadwal = 0;
            if ($periode === 'bulanan') {
                foreach ($jadwals as $j) {
                    $terjadwal += ($daysCount[$j->hari] ?? 0) * $j->count;
                }
            } else {
                $jHari = $jadwals->firstWhere('hari', $namaHari);
                $terjadwal = $jHari ? $jHari->count : 0;
            }

            $jurnalInfo = $jurnalGuruGrouped->get($g->id);
            $terisi = $jurnalInfo ? $jurnalInfo->count : 0;
            $terakhir = $jurnalInfo ? $jurnalInfo->terakhir : null;
            $kosong = max(0, $terjadwal - $terisi);

            return (object) [
                'id' => $g->id,
                'name' => $g->name,
                'nip' => $g->nip,
                'terjadwal' => $terjadwal,
                'terisi' => $terisi,
                'kosong' => $kosong,
                'terakhir' => $terakhir,
                'persentase' => $terjadwal > 0 ? min(100, round(($terisi / $terjadwal) * 100)) : ($terisi > 0 ? 100 : 0),
            ];
        });

        if ($guruId && $guruId !== 'all') {
            $rekapGuru = $rekapGuru->where('id', $guruId);
        }

        if ($request->filled('search')) {
            $termLower = strtolower(trim($request->query('search')));
            $rekapGuru = $rekapGuru->filter(function ($item) use ($termLower) {
                return str_contains(strtolower($item->name), $termLower) || str_contains(strtolower($item->nip ?? ''), $termLower);
            });
        }

        $requestDispensasi = Dispensasi::with('siswa.kelas')
            ->whereDate('tanggal', $tanggal)
            ->where('status_akhir', 'Pending')
            ->get();

        $countSemua = $allJurnals->count();
        $countGuruAbsen = 0;
        $countBelumValidasi = 0;
        $menungguValidasi = 0;

        return view('dashboard.admin.rekap-jurnal', compact(
            'jurnals',
            'kelases',
            'gurus',
            'rekapGuru',
            'periode',
            'tanggal',
            'bulan',
            'tahun',
            'guruId',
            'kelasId',
            'kehadiran',
            'validasi',
            'search',
            'keterlambatan',
            'totalKelas',
            'kelasLapor',
            'totalTerjadwal',
            'totalTerisi',
            'totalKosong',
            'guruHadir',
            'guruTidakHadir',
            'guruTerlambat',
            'guruAbsen',
            'menungguValidasi',
            'tab',
            'countSemua',
            'countBelumValidasi',
            'countGuruAbsen',
            'namaHari',
            'requestDispensasi'
        ));
    }

    /**
     * Download rekap jurnal sebagai PDF (Guru Piket).
     */
    public function downloadRekapJurnalPdf(Request $request): Response
    {
        $this->ensurePiketAccess();

        $periode = $request->query('periode', 'harian');
        $tanggal = $request->query('tanggal', now('Asia/Jakarta')->toDateString());
        $bulan = min(12, max(1, $request->integer('bulan', now('Asia/Jakarta')->month)));
        $tahun = min(2100, max(2026, $request->integer('tahun', now('Asia/Jakarta')->year)));

        $query = JurnalMengajar::with(['kelas', 'guru', 'mapel'])->orderByDesc('tanggal')->orderBy('jam_ke');
        if ($periode === 'bulanan') {
            $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            $periodeLabel = Carbon::create($tahun, $bulan)->translatedFormat('F Y');
        } else {
            $query->whereDate('tanggal', $tanggal);
            $periodeLabel = Carbon::parse($tanggal)->translatedFormat('d F Y');
        }

        $lines = $query->get()->map(function (JurnalMengajar $jurnal): string {
            return sprintf(
                '%s | Kelas %s | Jam %s | %s | %s | Hadir: %s',
                $jurnal->tanggal,
                $jurnal->kelas?->nama_kelas ?? '-',
                $jurnal->jam_ke,
                $jurnal->guru?->name ?? '-',
                $jurnal->mapel?->nama_mapel ?? '-',
                $jurnal->jumlah_hadir
            );
        })->all();

        return response(SimplePdfDocument::make('Rekap Jurnal Mengajar - '.$periodeLabel, array_merge(['Tanggal | Kelas | Jam | Guru | Mata Pelajaran | Kehadiran', str_repeat('-', 120)], $lines)), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rekap-jurnal-'.str($periodeLabel)->slug().'.pdf"',
        ]);
    }
}
