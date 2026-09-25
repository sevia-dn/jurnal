<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Dispensasi;
use App\Models\JadwalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\JurnalMengajar;
use App\Models\KehadiranGuru;
use App\Models\Kelas;
use App\Models\Notifikasi;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use App\Services\PiketScheduleService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public function utama()
    {
        if (! $this->canAccessPiket()) {
            return $this->notScheduledResponse();
        }

        $today = now('Asia/Jakarta')->toDateString();
        $journals = JurnalMengajar::with(['guru', 'kelas', 'mapel'])
            ->whereDate('tanggal', $today)
            ->latest('id_jurnal')
            ->get();

        // Guru hadir = yang sudah isi jurnal hari ini (unik per guru)
        $submittedTeacherIds = $journals->pluck('id_user')->unique();

        // Daftar guru hadir (unik per guru, ambil jurnal pertama mereka)
        $guruHadir = $journals->unique('id_user')->map(fn ($j) => [
            'nama' => $j->guru?->name ?? 'Guru',
            'status' => 'Hadir',
            'keterangan' => 'Jurnal terisi',
            'kelas' => $j->kelas?->nama_kelas ?? '-',
        ])->values();

        // Guru tidak hadir = yang dilaporkan piket (Sakit/Izin)
        $teacherAbsenceReports = KehadiranGuru::with('user')
            ->whereDate('tanggal', $today)
            ->whereIn('status', ['Sakit', 'Izin'])
            ->get();

        $dispensasiHistory = Dispensasi::with(['siswa.kelas', 'pembuat'])
            ->latest()
            ->take(10)
            ->get();

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
            'dispensasiHistory' => $dispensasiHistory,
            'today' => $today,
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

        // Absensi jurnal tetap menjadi data per sesi pembelajaran.
        $absensiRecords = Absensi::whereIn('id_siswa', $siswas->pluck('id'))
            ->whereHas('jurnal', function ($q) use ($tanggal) {
                $q->whereDate('tanggal', $tanggal);
            })
            ->orderBy('id', 'asc')
            ->get()
            ->keyBy('id_siswa');

        $studentsData = $siswas->map(function ($s) use ($dispensasis, $kehadiranPiket, $absensiRecords, $selectedKelas) {
            $dispen = $dispensasis->get($s->id);
            $piketRecord = $kehadiranPiket->get($s->id);
            $absen = $absensiRecords->get($s->id);

            if ($dispen) {
                $status = 'D';
                $catatan = 'Dispensasi: '.$dispen->deskripsi_waktu.' ('.$dispen->alasan.')';
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
                'nis' => $s->nis,
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
            'siswa_id' => 'required|exists:siswas,id',
            'jenis_dispensasi' => 'required|string',
            'mode_waktu' => 'required|in:sepanjang_hari,jam_tertentu',
            'jam_ke_mulai' => 'nullable|required_if:mode_waktu,jam_tertentu|integer|min:1',
            'jam_ke_selesai' => 'nullable|integer|gte:jam_ke_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string',
            'bukti' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:10240',
        ], [
            'jam_ke_mulai.required_if' => 'Jam ke mulai wajib dipilih jika memilih mode Jam Tertentu.',
            'jam_ke_selesai.gte' => 'Jam ke selesai harus sama atau lebih besar dari jam ke mulai.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $siswa = Siswa::with('kelas')->findOrFail($request->siswa_id);

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('dispensasi', 'public');
        }

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
            'siswa_id' => $siswa->id,
            'jenis_dispensasi' => $request->jenis_dispensasi,
            'tipe_dispensasi' => $tipeDispensasi,
            'jam_ke_mulai' => $jamKeMulai,
            'jam_ke_selesai' => $jamKeSelesai,
            'tanggal' => $tglMulai,
            'tanggal_selesai' => $tglSelesai,
            'alasan' => $request->alasan,
            'bukti' => $buktiPath,
            'status_piket' => 'disetujui',
            'status_waka' => 'menunggu',
            'status_akhir' => 'menunggu',
            'token_approval' => $tokenApproval,
            'dibuat_oleh' => auth()->id(),
        ]);

        $dispensasi->load(['siswa.kelas', 'pembuat']);

        // Trigger Notifikasi WhatsApp ke Waka
        $this->whatsAppService->sendDispensasiNotificationToWaka($dispensasi);

        $approvalUrl = route('dispensasi.approval', ['token' => $tokenApproval]);

        return redirect()->route('piket.dispensasi.form')
            ->with('success', "Pengajuan dispensasi untuk {$siswa->nama} berhasil dibuat.")
            ->with('approval_url', $approvalUrl)
            ->with('token_approval', $tokenApproval);
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

    private function canAccessPiket(): bool
    {
        $user = Auth::user();

        return $user !== null && (
            $user->role === 'admin'
            || $user->role === 'piket'
            || $this->piketScheduleService->isScheduledNow($user)
            || $user->isPiketActive()
        );
    }

    private function ensurePiketAccess(): void
    {
        abort_unless($this->canAccessPiket(), 403, 'Akses Guru Piket hanya untuk petugas yang dijadwalkan hari ini.');
    }
}
