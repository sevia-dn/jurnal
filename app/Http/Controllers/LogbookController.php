<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JurnalMengajar;
use App\Models\Siswa;
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
        Carbon::setLocale('id');

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $hariIni = $now->translatedFormat('l');
        $currentTime = $now->format('H:i');

        // 1. Validasi Presensi: Guru WAJIB melakukan presensi/absen masuk hari ini terlebih dahulu
        $attendance = TeacherAttendance::where('user_id', $user->id)
            ->where('date', $todayDate)
            ->first();

        if (! $attendance) {
            return redirect()
                ->route('guru.utama')
                ->with('error', 'Peringatan: Anda WAJIB melakukan presensi/absen masuk terlebih dahulu untuk hari ini sebelum dapat mengisi dan menyimpan Jurnal Pembelajaran.');
        }

        // Jika status absen adalah "Tidak Hadir", tidak bisa mengisi jurnal
        if ($attendance->status === 'Tidak Hadir') {
            return redirect()
                ->route('guru.utama')
                ->with('error', 'Anda tercatat Tidak Hadir pada hari ini sehingga tidak dapat mengisi jurnal pembelajaran kelas.');
        }

        // 2. Validasi input form jurnal, lampiran bukti hadir, dan absensi siswa
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'required|exists:mapels,id',
            'jam_ke' => 'required|integer|min:1|max:13',
            'jam_selesai' => 'nullable|integer|min:1|max:13|gte:jam_ke',
            'materi' => 'required|string|max:500',
            'ada_tugas' => 'required|in:Ya,Tidak',
            'catatan' => 'nullable|string',
            'lampiran' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'absensi' => 'nullable|array',
            'absensi.*' => 'nullable|in:Hadir,Sakit,Izin,Alpa',
        ], [
            'materi.required' => 'Materi / Pokok Pembahasan wajib diisi.',
            'jam_selesai.gte' => 'Jam selesai mengajar harus lebih besar atau sama dengan jam mulai.',
            'lampiran.required' => 'Lampiran foto atau berkas bukti kehadiran di kelas wajib diunggah.',
            'lampiran.file' => 'Lampiran harus berupa berkas/file yang valid.',
            'lampiran.mimes' => 'Format lampiran harus berupa foto (JPG, PNG, WebP) atau berkas PDF.',
            'lampiran.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
        ]);

        $jamMulai = (int) $request->jam_ke;
        $jamSelesai = (int) ($request->jam_selesai ?: $request->jam_ke);

        // 3. Batasan Waktu Jam Mengajar:
        // Ambil slot waktu mulai dan selesai mengajar
        $slotMulai = GuruController::getJamSlot($hariIni, $jamMulai);
        $slotSelesai = GuruController::getJamSlot($hariIni, $jamSelesai);
        $startSlot = $slotMulai['start'];
        $endSlot = $slotSelesai['end'];

        // Cek jika belum memasuki jam waktu mengajar
        if ($currentTime < $startSlot) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Jam pelajaran untuk sesi ini belum dimulai ({$startSlot} - {$endSlot} WIB). Anda hanya dapat mengisi jurnal setelah jam pelajaran dimulai.");
        }

        // Cek jika telah melewati jam waktu mengajar
        if ($currentTime > $endSlot) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Batas waktu pengisian jurnal untuk sesi ini ({$startSlot} - {$endSlot} WIB) telah terlewat. Anda tidak dapat mengisi jurnal setelah jam mengajar berakhir.");
        }

        // 4. Cek apakah guru sudah mengirimkan jurnal untuk kelas, mapel, tanggal, dan jam_ke yang sama
        $existing = JurnalMengajar::where('id_user', $user->id)
            ->where('id_kelas', $request->id_kelas)
            ->where('id_mapel', $request->id_mapel)
            ->where('tanggal', $todayDate)
            ->where('jam_ke', $jamMulai)
            ->exists();

        if ($existing) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Anda sudah pernah mengirimkan jurnal pembelajaran untuk kelas dan jam pelajaran ini pada hari ini.');
        }

        // 5. Upload lampiran bukti kehadiran guru di kelas (jika ada)
        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('jurnal-lampiran', 'public');
        }

        // 6. Hitung rekap absensi siswa di kelas yang dipilih
        // Ambil semua siswa yang terdaftar di kelas tersebut
        $daftarSiswaKelas = Siswa::where('kelas_id', $request->id_kelas)->get();
        $inputAbsensi = $request->input('absensi', []);

        $jmlHadir = 0;
        $jmlSakit = 0;
        $jmlIzin = 0;
        $jmlAlpa = 0;

        $absensiFinal = [];
        foreach ($daftarSiswaKelas as $s) {
            // Default status adalah 'Hadir' jika tidak ditentukan
            $st = $inputAbsensi[$s->id] ?? 'Hadir';
            $absensiFinal[$s->id] = $st;

            switch ($st) {
                case 'Sakit':
                    $jmlSakit++;
                    break;
                case 'Izin':
                    $jmlIzin++;
                    break;
                case 'Alpa':
                    $jmlAlpa++;
                    break;
                default:
                    $jmlHadir++;
                    break;
            }
        }
        $jmlTidakHadir = $jmlSakit + $jmlIzin + $jmlAlpa;

        // 7. Database Transaction
        DB::beginTransaction();
        try {
            $jurnal = JurnalMengajar::create([
                'id_user' => $user->id,
                'id_kelas' => $request->id_kelas,
                'id_mapel' => $request->id_mapel,
                'tanggal' => $todayDate,
                'jam_ke' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'materi' => $request->materi,
                'keterangan' => null,
                'jumlah_hadir' => $jmlHadir,
                'jumlah_sakit' => $jmlSakit,
                'jumlah_izin' => $jmlIzin,
                'jumlah_alpa' => $jmlAlpa,
                'jumlah_dispensasi' => 0,
                'jumlah_tidak_hadir' => $jmlTidakHadir,
                'status_kehadiran_guru' => 'Hadir',
                'ada_tugas' => $request->ada_tugas === 'Ya',
                'catatan' => $request->catatan,
                'lampiran' => $lampiranPath,
                'status_validasi' => 'belum_divalidasi',
                'catatan_validasi' => null,
                'divalidasi_pada' => null,
            ]);

            // Simpan detail absensi tiap siswa di kelas
            foreach ($absensiFinal as $idSiswa => $statusSiswa) {
                Absensi::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_siswa' => $idSiswa,
                    'status' => $statusSiswa,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('guru.riwayat')
                ->with('success', 'Jurnal pembelajaran dan presensi siswa berhasil disimpan! Status: Menunggu Validasi Pengurus Kelas.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan logbook: '.$e->getMessage());
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
