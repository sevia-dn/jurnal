<?php

namespace App\Http\Controllers;

use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\TeacherAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    /**
     * Mengambil slot waktu (mulai & selesai) untuk jam pelajaran tertentu
     * sesuai aturan jadwal KBM SMKN 1 Boyolangu:
     * - Senin s.d. Kamis: 1 JP = 40 menit (sampai jam ke-10, pulang 15:00)
     * - Jumat: 1 JP = 30 menit (Kelas XI sampai jam ke-12 pulang 15:00, Kelas X sampai jam ke-13 pulang 15:30)
     * sesuai aturan jadwal KBM resmi SMKN 1 Boyolangu:
     * - Senin s.d. Kamis:
     *   - Jam 1-4: @ 40 menit (07:00 - 09:40)
     *   - Istirahat 1: 09:40 - 10:00
     *   - Jam 5-7: @ 35 menit (10:00 - 11:45)
     *   - Istirahat 2 (Ishoma): 11:45 - 13:15
     *   - Jam 8-10: @ 35 menit (13:15 - 15:00)
     * - Jumat:
     *   - Jam 1-5: @ 30 menit (07:00 - 09:30)
     *   - Istirahat 1: 09:30 - 09:50
     *   - Jam 6-8: @ 30 menit (09:50 - 11:20)
     *   - Istirahat 2 (Sholat Jumat): 11:20 - 13:00
     *   - Jam 9-12 (Kelas XI pulang 15:10 setelah jam ke-12)
     *   - Jam 9-13 (Kelas X pulang 15:35 setelah jam ke-13)
     */
    public static function getJamSlot(string $hari, int $jamKe): array
    {
        $isJumat = in_array(strtolower(trim($hari)), ['jumat', 'jum\'at', 'friday']);

        if (! $isJumat) {
            $seninKamis = [
                1 => ['start' => '07:00', 'end' => '07:40'],
                2 => ['start' => '07:40', 'end' => '08:20'],
                3 => ['start' => '08:20', 'end' => '09:00'],
                4 => ['start' => '09:00', 'end' => '09:40'],
                5 => ['start' => '10:00', 'end' => '10:40'],
                6 => ['start' => '10:40', 'end' => '11:20'],
                7 => ['start' => '11:20', 'end' => '12:00'],
                8 => ['start' => '13:00', 'end' => '13:40'],
                9 => ['start' => '13:40', 'end' => '14:20'],
                10 => ['start' => '14:20', 'end' => '15:00'],
                5 => ['start' => '10:00', 'end' => '10:35'],
                6 => ['start' => '10:35', 'end' => '11:10'],
                7 => ['start' => '11:10', 'end' => '11:45'],
                8 => ['start' => '13:15', 'end' => '13:50'],
                9 => ['start' => '13:50', 'end' => '14:25'],
                10 => ['start' => '14:25', 'end' => '15:00'],
            ];

            return $seninKamis[$jamKe] ?? ['start' => '07:00', 'end' => '15:00'];
        }

        $jumat = [
            1 => ['start' => '07:00', 'end' => '07:30'],
            2 => ['start' => '07:30', 'end' => '08:00'],
            3 => ['start' => '08:00', 'end' => '08:30'],
            4 => ['start' => '08:30', 'end' => '09:00'],
            5 => ['start' => '09:00', 'end' => '09:30'],
            6 => ['start' => '09:50', 'end' => '10:20'],
            7 => ['start' => '10:20', 'end' => '10:50'],
            8 => ['start' => '10:50', 'end' => '11:20'],
            9 => ['start' => '13:00', 'end' => '13:30'],
            10 => ['start' => '13:30', 'end' => '14:00'],
            11 => ['start' => '14:00', 'end' => '14:30'],
            12 => ['start' => '14:30', 'end' => '15:00'],
            13 => ['start' => '15:00', 'end' => '15:30'],
            12 => ['start' => '14:30', 'end' => '15:10'],
            13 => ['start' => '15:00', 'end' => '15:35'],
        ];

        return $jumat[$jamKe] ?? ['start' => '07:00', 'end' => '15:30'];

        return $jumat[$jamKe] ?? ['start' => '07:00', 'end' => '15:35'];
    }

    /**
     * Halaman utama guru (Dashboard pribadi guru)
     */
    public function beranda(Request $request)
    {
        Carbon::setLocale('id');

        $user = Auth::user();

        $now = Carbon::now();
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $hariIni = $now->translatedFormat('l');
        $currentTime = $now->format('H:i');
        $currentFullTime = $now->format('H:i:s');

        // Jadwal guru khusus HARI INI (hari saat login)
        $jadwals = JadwalMengajar::with(['kelas', 'mapel'])
            ->where('id_user', $user->id)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // Cek status waktu dan status pengisian jurnal per jadwal hari ini
        foreach ($jadwals as $jadwal) {
            $slotMulai = self::getJamSlot($hariIni, (int) $jadwal->jam_mulai);
            $slotSelesai = self::getJamSlot($hariIni, (int) $jadwal->jam_selesai);

            $jadwal->waktu_mulai = $slotMulai['start'];
            $jadwal->waktu_selesai = $slotSelesai['end'];

            // Cek apakah jurnal untuk sesi ini sudah diisi
            $jadwal->is_filled = JurnalMengajar::where('id_user', $user->id)
                ->where('id_kelas', $jadwal->id_kelas)
                ->where('id_mapel', $jadwal->id_mapel)
                ->where('tanggal', $todayDate)
                ->where('jam_ke', $jadwal->jam_mulai)
                ->exists();

            // Batasan waktu: jika waktu sekarang sudah melebihi waktu selesai sesi mengajar
            if ($currentTime > $jadwal->waktu_selesai) {
                $jadwal->status_waktu = 'lewat'; // sudah kelewat jam mengajar
            } elseif ($currentTime < $jadwal->waktu_mulai) {
                $jadwal->status_waktu = 'belum_mulai';
            } else {
                $jadwal->status_waktu = 'berlangsung'; // sedang dalam jam mengajar
            }
        }

        // Cek apakah ada minimal 1 jurnal yang sudah diisi hari ini
        $hasSubmittedJournal = JurnalMengajar::where('id_user', $user->id)
            ->where('tanggal', $todayDate)
            ->exists();

        // Jadwal aktif pertama hari ini yang belum terlewat atau belum diisi untuk auto-fill
        $activeJadwal = $jadwals->firstWhere('is_filled', false) ?? $jadwals->first();

        // Data pendukung form
        $teachers = User::where('role', 'guru')
            ->orderBy('name')
            ->get();

        $kelases = Kelas::orderBy('nama_kelas')
            ->get();

        $mapels = Mapel::orderBy('nama_mapel')
            ->get();

        // Siswa dikirim ke view untuk daftar presensi di kelas terpilih
        $siswas = Siswa::with('kelas')
            ->orderBy('nama')
            ->get();

        return view('dashboard.guru-pengajar.utama', compact(
            'user',
            'hasSubmittedJournal',
            'jadwals',
            'activeJadwal',
            'teachers',
            'kelases',
            'mapels',
            'siswas',
            'hariIni',
            'todayDate',
            'currentTime',
            'currentFullTime'
        ));
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

        $todayDate = Carbon::today()->toDateString();
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
