<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\KehadiranGuru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Notifikasi;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Services\LogbookDeadlinePolicy;
use App\Services\PiketScheduleService;
use App\Services\ScheduleTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    /**
     * Halaman utama guru (Dashboard pribadi guru)
     */
    public function beranda(
        Request $request,
        PiketScheduleService $piketScheduleService,
        LogbookDeadlinePolicy $deadlinePolicy,
        ScheduleTimeService $scheduleTimeService,
    ) {
        Carbon::setLocale('id');

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $hariIni = $now->translatedFormat('l');
        $currentTime = $now->format('H:i');
        $currentFullTime = $now->format('H:i:s');
        $logbookPolicy = $deadlinePolicy->configuration();

        $isPiketActive = $piketScheduleService->isScheduledNow($user);
        $isWaka = $user->isWaka();

        // Absensi guru hari ini (via TeacherAttendance untuk form storeAbsen)
        $attendance = TeacherAttendance::where('user_id', $user->id)
            ->where('date', $todayDate)
            ->first();
        $hasCheckedIn = $attendance !== null;

        // Kehadiran guru (via KehadiranGuru untuk tombol absen masuk)
        $kehadiranHariIni = KehadiranGuru::where('user_id', $user->id)
            ->whereDate('tanggal', $todayDate)
            ->first();
        $sudahAbsen = $kehadiranHariIni !== null;

        $pendingDispensasis = collect();
        if ($isWaka) {
            $pendingDispensasis = Dispensasi::with(['siswa', 'pembuat'])
                ->where('status_waka', 'menunggu')
                ->latest()
                ->get();
        }

        $allDispensasis = Dispensasi::with(['siswa', 'pembuat', 'pemroses'])
            ->latest()
            ->take(5)
            ->get();
        // Jadwal guru khusus HARI INI (hari saat login)
        $jadwals = JadwalMengajar::with(['kelas', 'mapel'])
            ->where('id_user', $user->id)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // Cek status waktu dan status pengisian jurnal per jadwal hari ini
        foreach ($jadwals as $jadwal) {
            $slotMulai = $scheduleTimeService->slot($hariIni, (int) $jadwal->jam_mulai);
            $slotSelesai = $scheduleTimeService->slot($hariIni, (int) $jadwal->jam_selesai);

            $jadwal->waktu_mulai = $slotMulai['start'];
            $jadwal->waktu_selesai = $slotSelesai['end'];

            // Cek apakah jurnal untuk sesi ini sudah diisi
            $jadwal->is_filled = JurnalMengajar::where('id_user', $user->id)
                ->where('id_kelas', $jadwal->id_kelas)
                ->where('id_mapel', $jadwal->id_mapel)
                ->where('tanggal', $todayDate)
                ->where('jam_ke', $jadwal->jam_mulai)
                ->exists();

            // Status waktu berdasarkan jam sekarang
            if ($currentTime > $jadwal->waktu_selesai) {
                $jadwal->status_waktu = 'lewat';
            } elseif ($currentTime < $jadwal->waktu_mulai) {
                $jadwal->status_waktu = 'belum_mulai';
            } else {
                $jadwal->status_waktu = 'berlangsung';
            }
        }

        // Cek apakah ada minimal 1 jurnal yang sudah diisi hari ini
        $hasSubmittedJournal = JurnalMengajar::where('id_user', $user->id)
            ->where('tanggal', $todayDate)
            ->exists();

        // Jadwal aktif pertama hari ini yang belum terlewat atau belum diisi untuk auto-fill
        $activeJadwal = $jadwals->firstWhere('is_filled', false) ?? $jadwals->first();

        // Data pendukung form
        $teachers = User::where('role', 'guru')->orderBy('name')->get();
        $kelases = Kelas::orderBy('nama_kelas')->get();
        $mapels = Mapel::orderBy('nama_mapel')->get();

        // Siswa dikirim ke view untuk daftar presensi di kelas terpilih
        $siswas = Siswa::with('kelas')
            ->orderBy('kelas_id')
            ->orderBy('nama')
            ->get();
        // Siswa dikirim ke view dikelompokkan per kelas_id untuk presensi instan tanpa lag
        $siswasByKelas = Siswa::orderBy('nama')
            ->get(['id', 'nama', 'nis', 'jenis_kelamin', 'kelas_id'])
            ->groupBy('kelas_id');
        $piketKehadiranHariIni = PiketKehadiranSiswa::query()
            ->whereDate('tanggal', $todayDate)
            ->get()
            ->keyBy('siswa_id');

        return view('dashboard.guru-pengajar.utama', compact(
            'user',
            'isPiketActive',
            'isWaka',
            'pendingDispensasis',
            'allDispensasis',
            'sudahAbsen',
            'kehadiranHariIni',
            'attendance',
            'hasCheckedIn',
            'jadwals',
            'hasSubmittedJournal',
            'activeJadwal',
            'teachers',
            'kelases',
            'mapels',
            'siswas',
            'siswasByKelas',
            'piketKehadiranHariIni',
            'hariIni',
            'todayDate',
            'currentTime',
            'currentFullTime',
            'logbookPolicy',
        ));
    }

    /**
     * Proses Absen Masuk / Lapor Kehadiran Guru
     */
    public function absenMasuk(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $today = now()->toDateString();
        $jamSekarang = now()->format('H:i:s');

        // Gunakan updateOrCreate agar tidak duplicate jika diklik 2x
        KehadiranGuru::updateOrCreate(
            [
                'user_id' => $user->id,
                'tanggal' => $today,
            ],
            [
                'jam_masuk' => $jamSekarang,
                'status' => 'Hadir',
            ]
        );

        return back()->with(
            'success',
            'Kehadiran masuk berhasil dilaporkan pada pukul '.substr($jamSekarang, 0, 5).' WIB.'
        );
    }

    public function markNotificationRead(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->id_user === Auth::id(), 404);

        $notifikasi->update(['is_read' => true]);

        return redirect()->route('guru.riwayat');
    }

    public function markAllNotificationsRead()
    {
        Notifikasi::where('id_user', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back();
    }

    /**
     * Simpan absensi guru
     */
    public function storeAbsen(Request $request)
    {
        $teacherId = $request->teacher_id ?: Auth::id();

        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'nip' => 'nullable|string',
            'status_kehadiran_guru' => 'required|in:Hadir,Tidak Hadir',
            'reason' => 'required_if:status_kehadiran_guru,Tidak Hadir|nullable|string|max:500',
            'proof_file' => 'nullable|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ], [
            'reason.required_if' => 'Alasan wajib diisi jika memilih status Tidak Hadir.',
            'proof_file.mimes' => 'Format berkas bukti harus berupa gambar (JPG, PNG, WebP) atau dokumen PDF.',
            'proof_file.max' => 'Ukuran berkas bukti maksimal 5 MB.',
        ]);

        $todayDate = Carbon::today('Asia/Jakarta')->toDateString();

        $existingAttendance = TeacherAttendance::where('user_id', $teacherId)
            ->where('date', $todayDate)
            ->first();

        if ($existingAttendance) {
            return redirect()
                ->back()
                ->with('error', 'Absensi untuk guru tersebut sudah dilakukan hari ini.');
        }

        $path = null;

        if ($request->hasFile('proof_file')) {
            $path = $request->file('proof_file')
                ->store('teacher-attendances', 'public');
        }

        TeacherAttendance::create([
            'user_id' => $teacherId,
            'date' => $todayDate,
            'status' => $request->status_kehadiran_guru,
            'reason' => $request->reason,
            'proof_file' => $path,
        ]);

        return redirect()
            ->route('guru.utama')
            ->with(
                'success',
                'Presensi/Absen berhasil dicatat. '.($request->status_kehadiran_guru === 'Hadir' ? 'Silakan lanjutkan mengisi jurnal pembelajaran.' : 'Laporan ketidakhadiran Anda telah disimpan.')
            );
    }
}
