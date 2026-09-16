<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherAttendance;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\User;
use App\Models\JadwalMengajar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    /**
     * Halaman utama guru
     */
    public function beranda()
    {
        Carbon::setLocale('id');

        $user = Auth::user();

        $now = Carbon::now();
        $todayDate = $now->toDateString();
        $hariIni = $now->translatedFormat('l');

        // Absensi guru hari ini
        $attendance = TeacherAttendance::where('user_id', $user->id)
            ->where('date', $todayDate)
            ->first();

        $hasCheckedIn = $attendance ? true : false;

        // Cek apakah sudah mengisi jurnal hari ini
        $hasSubmittedJournal = JurnalMengajar::where('id_user', $user->id)
            ->where('tanggal', $todayDate)
            ->exists();

        // Jadwal guru hari ini
        $jadwals = JadwalMengajar::with(['kelas', 'mapel'])
            ->where('id_user', $user->id)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();
        // Data form
        $teachers = User::where('role', 'guru')
            ->orderBy('name')
            ->get();

        $kelases = Kelas::orderBy('nama_kelas')
            ->get();

        $mapels = Mapel::orderBy('nama_mapel')
            ->get();

        // SEMUA siswa dikirim ke view.
        // Nanti Blade akan memfilter berdasarkan kelas yang dipilih.
        $siswas = Siswa::with('kelas')
            ->orderBy('nama')
            ->get();

        return view('dashboard.guru-pengajar.utama', compact(
            'user',
            'attendance',
            'hasCheckedIn',
            'hasSubmittedJournal',
            'jadwals',
            'teachers',
            'kelases',
            'mapels',
            'siswas'
        ));
    }

    /**
     * Simpan absensi guru
     */
    public function storeAbsen(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'nip' => 'nullable|string',
            'status_kehadiran_guru' => 'required|in:Hadir,Izin,Sakit,Tanpa Keterangan',
            'reason' => 'nullable|string|max:255',
            'proof_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $todayDate = Carbon::today()->toDateString();

        $existingAttendance = TeacherAttendance::where(
            'user_id',
            $request->teacher_id
        )
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
            'user_id' => $request->teacher_id,
            'date' => $todayDate,
            'status' => $request->status_kehadiran_guru,
            'reason' => $request->reason,
            'proof_file' => $path,
        ]);

        return redirect()
            ->route('guru.utama')
            ->with(
                'success',
                'Absen masuk berhasil dicatat. Silakan lanjutkan mengisi jurnal pembelajaran.'
            );
    }
}