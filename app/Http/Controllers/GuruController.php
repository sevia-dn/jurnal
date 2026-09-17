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
     * Halaman utama guru (Dashboard pribadi guru)
     */
    public function beranda(Request $request)
    {
        Carbon::setLocale('id');

        $user = Auth::user();

        $now = Carbon::now();
        $todayDate = $now->toDateString();
        $hariIni = $now->translatedFormat('l');

        // Hari yang dipilih (default adalah hari saat guru login)
        $selectedHari = $request->query('hari', $hariIni);

        // Absensi guru hari ini
        $attendance = TeacherAttendance::where('user_id', $user->id)
            ->where('date', $todayDate)
            ->first();

        $hasCheckedIn = $attendance ? true : false;

        // Cek apakah sudah mengisi jurnal hari ini
        $hasSubmittedJournal = JurnalMengajar::where('id_user', $user->id)
            ->where('tanggal', $todayDate)
            ->exists();

        // Jadwal guru untuk hari yang dipilih (default hari login)
        $jadwals = JadwalMengajar::with(['kelas', 'mapel'])
            ->where('id_user', $user->id)
            ->where('hari', $selectedHari)
            ->orderBy('jam_mulai')
            ->get();

        // Ringkasan jumlah jam/sesi mengajar guru per hari
        $jadwalCounts = JadwalMengajar::where('id_user', $user->id)
            ->selectRaw('hari, count(*) as total')
            ->groupBy('hari')
            ->pluck('total', 'hari');

        // Jadwal aktif pertama hari ini untuk auto-fill form logbook
        $activeJadwal = $jadwals->first();

        // Data pendukung form
        $teachers = User::where('role', 'guru')
            ->orderBy('name')
            ->get();

        $kelases = Kelas::orderBy('nama_kelas')
            ->get();

        $mapels = Mapel::orderBy('nama_mapel')
            ->get();

        // Siswa dikirim ke view untuk filter otomatis per kelas
        $siswas = Siswa::with('kelas')
            ->orderBy('nama')
            ->get();

        return view('dashboard.guru-pengajar.utama', compact(
            'user',
            'attendance',
            'hasCheckedIn',
            'hasSubmittedJournal',
            'jadwals',
            'activeJadwal',
            'teachers',
            'kelases',
            'mapels',
            'siswas',
            'hariIni',
            'selectedHari',
            'jadwalCounts'
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
            'reason' => 'nullable|string|max:500',
            'proof_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $todayDate = Carbon::today()->toDateString();

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
                'Presensi/Absen masuk berhasil dicatat. Silakan lanjutkan mengisi jurnal pembelajaran.'
            );
    }
}
