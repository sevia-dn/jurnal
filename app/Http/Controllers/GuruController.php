<?php

namespace App\Http\Controllers;

use App\Models\Dispensasi;
use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\PengaturanJurnal;
use App\Models\Siswa;
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
        $isJumat = in_array(strtolower(trim($hari)), ['jumat', 'friday']);

        if (! $isJumat) {
            $seninKamis = [
                1 => ['start' => '07:00', 'end' => '07:40'],
                2 => ['start' => '07:40', 'end' => '08:20'],
                3 => ['start' => '08:20', 'end' => '09:00'],
                4 => ['start' => '09:00', 'end' => '09:40'],
                // Istirahat 09:40 - 10:00
                5 => ['start' => '10:00', 'end' => '10:35'],
                6 => ['start' => '10:35', 'end' => '11:10'],
                7 => ['start' => '11:10', 'end' => '11:45'],
                // Ishoma 11:45 - 13:15
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
            // Istirahat 09:30 - 09:50
            6 => ['start' => '09:50', 'end' => '10:20'],
            7 => ['start' => '10:20', 'end' => '10:50'],
            8 => ['start' => '10:50', 'end' => '11:20'],
            // Sholat Jumat 11:20 - 13:00
            9 => ['start' => '13:00', 'end' => '13:30'],
            10 => ['start' => '13:30', 'end' => '14:00'],
            11 => ['start' => '14:00', 'end' => '14:30'],
            12 => ['start' => '14:30', 'end' => '15:10'],
            13 => ['start' => '15:10', 'end' => '15:35'],
        ];

        return $jumat[$jamKe] ?? ['start' => '07:00', 'end' => '15:35'];
    }

    /**
     * Halaman utama guru (Dashboard pribadi guru)
     */
    public function beranda(Request $request)
    {
        Carbon::setLocale('id');

        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $hariIni = $now->translatedFormat('l');
        $currentTime = $now->format('H:i');

        $isPiketActive = $user->isPiketActive();
        $isWaka = $user->isWaka();

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

        // Kebijakan tenggat pengisian jurnal
        $pengaturan = PengaturanJurnal::getKebijakanAktif();
        $kebijakanTenggat = $pengaturan->kebijakan_tenggat ?? 'jam_mengajar';

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

            // Status waktu berdasarkan jam sekarang dan kebijakan aktif
            if ($kebijakanTenggat === 'jam_mengajar') {
                if ($currentTime > $jadwal->waktu_selesai) {
                    $jadwal->status_waktu = 'lewat';
                } elseif ($currentTime < $jadwal->waktu_mulai) {
                    $jadwal->status_waktu = 'belum_mulai';
                } else {
                    $jadwal->status_waktu = 'berlangsung';
                }
            } else {
                // Untuk kebijakan 'hari_ini' dan 'longgar', selalu terbuka sepanjang hari
                $jadwal->status_waktu = 'berlangsung';
            }
        }

        // Cek apakah guru sudah pernah mengisi minimal 1 jurnal hari ini
        // (Jika FALSE: foto WAJIB diunggah pada jurnal pertama sebagai bukti presensi;
        //  Jika TRUE: foto OPSIONAL pada jurnal ke-2, ke-3, dst)
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
        $siswas = Siswa::with('kelas')->orderBy('nama')->get();

        $currentFullTime = $now->format('H:i:s');

        return view('dashboard.guru-pengajar.utama', compact(
            'user',
            'isPiketActive',
            'isWaka',
            'pendingDispensasis',
            'allDispensasis',
            'jadwals',
            'hasSubmittedJournal',
            'activeJadwal',
            'teachers',
            'kelases',
            'mapels',
            'siswas',
            'hariIni',
            'currentFullTime',
            'kebijakanTenggat',
        ));
    }
}
