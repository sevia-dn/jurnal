<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SekretarisController extends Controller
{
    /**
     * Dashboard Utama Sekretaris (Sinkron Real-time dari Database)
     */
    public function jurnalIndex()
    {
        $unreadCount = 0;
        $today = now()->toDateString();
        $namaHari = match (now()->dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };

        // Total sesi kelas hari ini dari jadwal pelajaran
        $totalSesi = JadwalPelajaran::where('hari', $namaHari)->count();
        if ($totalSesi === 0) {
            $totalSesi = 10;
        }

        // Jurnal menunggu validasi (guru tidak hadir/butuh konfirmasi)
        $perluPersetujuan = JurnalMengajar::whereDate('tanggal', $today)
            ->where('status_kehadiran_guru', '!=', 'Hadir')
            ->count();

        // Kehadiran Guru Hari Ini
        $guruHadir = JurnalMengajar::whereDate('tanggal', $today)->where('status_kehadiran_guru', 'Hadir')->count();
        $guruTotal = JurnalMengajar::whereDate('tanggal', $today)->count();
        if ($guruTotal === 0) {
            $guruTotal = $totalSesi;
        }

        // Kehadiran Siswa Hari Ini
        $siswaHadir = JurnalMengajar::whereDate('tanggal', $today)->sum('jumlah_hadir');
        $siswaTotal = Siswa::count();
        if ($siswaTotal === 0) {
            $siswaTotal = 36;
        }

        return view('jurnal.index', compact(
            'unreadCount',
            'today',
            'namaHari',
            'totalSesi',
            'perluPersetujuan',
            'guruHadir',
            'guruTotal',
            'siswaHadir',
            'siswaTotal'
        ));
    }

    /**
     * Halaman Input / Konfirmasi Jurnal
     */
    public function jurnalCreate()
    {
        $unreadCount = 0;
        $kelases = Kelas::orderBy('nama_kelas')->get();
        $gurus = User::where('role', 'guru')->orderBy('name')->get();
        $mapels = Mapel::orderBy('nama_mapel')->get();

        return view('jurnal.create', compact('unreadCount', 'kelases', 'gurus', 'mapels'));
    }

    /**
     * Halaman Jadwal 10 Sesi
     */
    public function jadwal()
    {
        $unreadCount = 0;
        $namaHari = match (now()->dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };
        $user = Auth::user();
        $kelas = null;
        if ($user) {
            $cleanUser = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $user->username));
            $kelas = Kelas::all()->first(function ($k) use ($cleanUser) {
                return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $k->nama_kelas)) === $cleanUser;
            });
        }
        if (!$kelas) {
            $kelas = Kelas::first();
        }

        $query = JadwalPelajaran::with(['kelas', 'guru'])
            ->where('hari', $namaHari);
        if ($kelas) {
            $query->where('id_kelas', $kelas->id_kelas);
        }
        $jadwals = $query->orderBy('jam_mulai', 'asc')->get();

        return view('jurnal.jadwal.index', compact('unreadCount', 'jadwals', 'namaHari', 'kelas'));
    }

    /**
     * Halaman Notifikasi Sekretaris
     */
    public function notifikasi(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $unreadCount = 0;
        $notifikasis = collect();
        $countSemua = 0;
        $countUnread = 0;
        $countValidasi = 0;
        $countIzin = 0;

        return view('notifikasi.index', compact(
            'notifikasis',
            'tab',
            'unreadCount',
            'countSemua',
            'countUnread',
            'countValidasi',
            'countIzin'
        ));
    }
}
