<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JurnalMengajar;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    /**
     * Menyimpan Jurnal Pembelajaran dan Rekap Absensi Siswa oleh Guru
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $todayDate = Carbon::today()->toDateString();

        // 1. Validasi Presensi: Guru WAJIB melakukan presensi/absen masuk hari ini terlebih dahulu
        $attendance = TeacherAttendance::where('user_id', $user->id)
            ->where('date', $todayDate)
            ->first();

        if (! $attendance) {
            return redirect()
                ->route('guru.utama')
                ->with('error', 'Peringatan: Anda WAJIB melakukan absen/presensi masuk terlebih dahulu untuk hari ini sebelum dapat mengisi dan menyimpan Jurnal Pembelajaran.');
        }

        // 2. Validasi input form jurnal, lampiran bukti hadir, dan absensi siswa
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mapels,id',
            'jam_ke' => 'required|integer',
            'materi' => 'required|string|max:255',
            'ada_tugas' => 'required|in:Ya,Tidak',
            'catatan' => 'nullable|string',
            'lampiran' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

            // Absensi siswa berbentuk array [id_siswa => status]
            'absensi' => 'required|array',
            'absensi.*' => 'required|in:Hadir,Sakit,Izin,Alpa',
        ]);

        // 3. Cek apakah guru sudah mengirimkan jurnal hari ini
        $existing = JurnalMengajar::where('id_user', $user->id)
            ->where('tanggal', $todayDate)
            ->exists();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah mengirimkan jurnal pembelajaran untuk hari ini.');
        }

        // 4. Upload lampiran bukti kehadiran guru di kelas (jika ada)
        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('jurnal-lampiran', 'public');
        }

        // 5. Hitung rekap jumlah kehadiran siswa secara otomatis dari array input
        $absensiData = $request->input('absensi');
        $jmlHadir = 0;
        $jmlSakit = 0;
        $jmlIzin = 0;
        $jmlAlpa = 0;

        foreach ($absensiData as $status) {
            switch ($status) {
                case 'Hadir':
                    $jmlHadir++;
                    break;
                case 'Sakit':
                    $jmlSakit++;
                    break;
                case 'Izin':
                    $jmlIzin++;
                    break;
                case 'Alpa':
                    $jmlAlpa++;
                    break;
            }
        }
        $jmlTidakHadir = $jmlSakit + $jmlIzin + $jmlAlpa;

        // 6. Database Transaction
        DB::beginTransaction();
        try {
            $jurnal = JurnalMengajar::create([
                'id_user' => $user->id,
                'id_kelas' => $request->id_kelas,
                'id_mapel' => $request->id_mapel,
                'tanggal' => $todayDate,
                'jam_ke' => $request->jam_ke,
                'materi' => $request->materi,
                'jumlah_hadir' => $jmlHadir,
                'jumlah_sakit' => $jmlSakit,
                'jumlah_izin' => $jmlIzin,
                'jumlah_alpa' => $jmlAlpa,
                'jumlah_tidak_hadir' => $jmlTidakHadir,
                'status_kehadiran_guru' => 'Hadir',
                'ada_tugas' => $request->ada_tugas === 'Ya',
                'catatan' => $request->catatan,
                'lampiran' => $lampiranPath,
                'status_validasi' => 'belum_divalidasi',
            ]);

            // Simpan detail absensi siswa
            foreach ($absensiData as $idSiswa => $statusSiswa) {
                Absensi::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_siswa' => $idSiswa,
                    'status' => $statusSiswa,
                ]);
            }

            DB::commit();

            return redirect()->route('guru.riwayat')->with('success', 'Jurnal pembelajaran dan absensi siswa berhasil dikirim! Status saat ini: Menunggu Validasi Pengurus Kelas.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan Halaman Riwayat & Rekap Jurnal Mengajar Guru
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $keyword = trim($request->query('keyword', ''));
        $filterStart = $request->query('start_date');
        $filterEnd = $request->query('end_date');

        $query = JurnalMengajar::with(['kelas', 'mapel', 'absensis.siswa'])
            ->where('id_user', $user->id);

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('materi', 'like', "%{$keyword}%")
                    ->orWhere('catatan', 'like', "%{$keyword}%")
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
            'notifikasiDisetujui'
        ));
    }
}
