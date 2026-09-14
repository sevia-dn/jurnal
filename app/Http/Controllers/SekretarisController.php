<?php

namespace App\Http\Controllers;

use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SekretarisController extends Controller
{
    /**
     * Dashboard Utama Sekretaris
     */
    public function jurnalIndex()
    {
        $unreadCount = Notifikasi::where('is_read', false)->count();

        // Statistik
        $perluPersetujuan = JurnalMengajar::where('status_validasi', 'Menunggu')->count();
        $today = now()->toDateString();
        $kehadiranGuruHadir = JurnalMengajar::whereDate('tanggal', $today)->where('status_kehadiran_guru', 'Hadir')->count();
        $kehadiranGuruTotal = JurnalMengajar::whereDate('tanggal', $today)->count();

        $kehadiranSiswaHadir = JurnalMengajar::whereDate('tanggal', $today)->sum('jumlah_hadir');
        $kehadiranSiswaTotal = JurnalMengajar::whereDate('tanggal', $today)
            ->selectRaw('SUM(jumlah_hadir + jumlah_sakit + jumlah_izin + jumlah_alpa + jumlah_dispensasi) as total')
            ->value('total') ?? 0;

        return view('jurnal.index', compact(
            'unreadCount',
            'perluPersetujuan',
            'kehadiranGuruHadir',
            'kehadiranGuruTotal',
            'kehadiranSiswaHadir',
            'kehadiranSiswaTotal'
        ));
    }

    /**
     * Halaman Input / Konfirmasi Jurnal
     */
    public function jurnalCreate()
    {
        $unreadCount = Notifikasi::where('is_read', false)->count();
        $kelases = Kelas::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })->orderBy('nama_kelas')->get();

        $gurus = User::where('role', 'guru')
            ->where(function ($q) {
                $q->where('status', 'aktif')->orWhereNull('status');
            })->orderBy('name')->get();

        $mapels = Mapel::where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        })->orderBy('nama_mapel')->get();

        return view('jurnal.create', compact('unreadCount', 'kelases', 'gurus', 'mapels'));
    }

    /**
     * Halaman Jadwal 10 Sesi
     */
    public function jadwal()
    {
        $unreadCount = Notifikasi::where('is_read', false)->count();
        return view('jurnal.jadwal.index', compact('unreadCount'));
    }

    /**
     * Halaman Notifikasi Sekretaris
     */
    public function notifikasi(Request $request)
    {
        $tab = $request->query('tab', 'all');

        $query = Notifikasi::with(['kelas', 'jurnal.mapel', 'jurnal.guru'])->latest();

        if ($tab === 'unread') {
            $query->where('is_read', false);
        } elseif ($tab === 'validasi') {
            $query->where('tipe', 'validasi');
        } elseif ($tab === 'izin') {
            $query->whereIn('tipe', ['izin', 'sakit', 'inval']);
        }

        $notifikasis = $query->paginate(20)->withQueryString();

        $countSemua = Notifikasi::count();
        $countUnread = Notifikasi::where('is_read', false)->count();
        $countValidasi = Notifikasi::where('tipe', 'validasi')->count();
        $countIzin = Notifikasi::whereIn('tipe', ['izin', 'sakit', 'inval'])->count();

        return view('notifikasi.index', compact(
            'notifikasis',
            'tab',
            'countSemua',
            'countUnread',
            'countValidasi',
            'countIzin'
        ));
    }

    /**
     * Tandai 1 Notifikasi Sudah Dibaca
     */
    public function readNotifikasi($id)
    {
        $notif = Notifikasi::findOrFail($id);
        $notif->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Tandai Semua Notifikasi Sudah Dibaca
     */
    public function readAllNotifikasi()
    {
        Notifikasi::where('is_read', false)->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi berhasil ditandai sebagai dibaca.');
    }
}
