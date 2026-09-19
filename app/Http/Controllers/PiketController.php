<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PiketController extends Controller
{
    /**
     * Helper untuk mengambil data tim piket hari ini dan backup waka
     */
    private function getPiketTeam($tanggal = null)
    {
        $date = $tanggal ? Carbon::parse($tanggal) : now();
        $namaHari = match ($date->dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };

        $piketGuru = JadwalPiket::with('user')->where('hari', $namaHari)->where('tipe', 'guru')->first();
        $piketWaka = JadwalPiket::with('user')->where('hari', $namaHari)->where('tipe', 'waka')->first();
        $allWakaUsers = JadwalPiket::where('tipe', 'waka')->with('user')->get()->pluck('user')->filter()->unique('id');
        $backupWakas = $allWakaUsers->filter(fn($u) => !$piketWaka || $u->id !== $piketWaka->user_id);
        if ($backupWakas->isEmpty()) {
            $backupWakas = User::where('role', 'guru')->where('id', '!=', optional($piketWaka)->user_id)->limit(3)->get();
        }

        return [
            'namaHari' => $namaHari,
            'piketGuru' => $piketGuru,
            'piketWaka' => $piketWaka,
            'allWakas' => $allWakaUsers,
            'backupWakas' => $backupWakas,
        ];
    }

    /**
     * Dashboard Utama Piket & Waka Backup
     */
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $piketTeam = $this->getPiketTeam($today);

        $jurnals = JurnalMengajar::with(['kelas', 'guru', 'mapel'])
            ->whereDate('tanggal', $today)
            ->orderBy('jam_ke')
            ->get();

        $totalKelas = Kelas::count();
        $guruHadir = $jurnals->where('status_kehadiran_guru', 'Hadir')->count();
        $guruAbsen = $jurnals->where('status_kehadiran_guru', '!=', 'Hadir')->count();
        $guruTerlambat = $jurnals->where('menit_keterlambatan', '>', 0)->count();
        $pendingValidation = $jurnals->where('status_kehadiran_guru', '!=', 'Hadir')->count();

        return view('dashboard.piket.utama', array_merge($piketTeam, [
            'jurnals' => $jurnals,
            'totalKelas' => $totalKelas,
            'guruHadir' => $guruHadir,
            'guruAbsen' => $guruAbsen,
            'guruTerlambat' => $guruTerlambat,
            'pendingValidation' => $pendingValidation,
        ]));
    }

    /**
     * Rekap Kehadiran Guru
     */
    public function kehadiran(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());
        $piketTeam = $this->getPiketTeam($tanggal);

        $gurus = User::where('role', 'guru')
            ->orderBy('name')
            ->get();

        $jurnalsHariIni = JurnalMengajar::whereDate('tanggal', $tanggal)->get()->keyBy('id_user');

        return view('dashboard.piket.kehadiran', array_merge($piketTeam, [
            'gurus' => $gurus,
            'jurnalsHariIni' => $jurnalsHariIni,
            'tanggal' => $tanggal,
        ]));
    }

    /**
     * Pengajuan & Approval Dispensasi Siswa
     */
    public function dispensasi(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());
        $piketTeam = $this->getPiketTeam($tanggal);

        $dispensasis = Dispensasi::with('siswa.kelas')
            ->orderBy('created_at', 'desc')
            ->get();

        $siswas = Siswa::with('kelas')->orderBy('nama')->get();

        return view('dashboard.piket.dispensasi', array_merge($piketTeam, [
            'dispensasis' => $dispensasis,
            'siswas' => $siswas,
            'tanggal' => $tanggal,
        ]));
    }

    /**
     * Kehadiran Siswa
     */
    public function kehadiranSiswa(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());
        $piketTeam = $this->getPiketTeam($tanggal);
        $kelases = Kelas::orderBy('nama_kelas')->get();

        return view('dashboard.piket.kehadiran-siswa', array_merge($piketTeam, [
            'kelases' => $kelases,
            'tanggal' => $tanggal,
        ]));
    }

    /**
     * Validasi Izin Guru oleh Piket atau Waka Backup
     */
    public function validasiIzinGuru(Request $request, $id)
    {
        $jurnal = JurnalMengajar::findOrFail($id);
        $status = $request->input('status', 'Izin');

        $jurnal->update([
            'status_kehadiran_guru' => $status,
            'catatan' => $request->input('catatan', $jurnal->catatan),
        ]);

        return redirect()->back()->with('success', 'Validasi izin guru berhasil diperbarui.');
    }
}
