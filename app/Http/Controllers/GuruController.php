<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengaturan;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    /**
     * Halaman Input Logbook Guru dengan Validasi Jadwal & Keterlambatan
     */
    public function logbookCreate(Request $request)
    {
        $user = Auth::user();
        $currentTime = now()->format('H:i:s');
        
        $tenggatStatus = (int) Pengaturan::getValue('tenggat_status', 1);
        $tenggatOpsi = (string) Pengaturan::getValue('tenggat_opsi', 'terbatas_jam');
        $isModeLos = ($tenggatStatus === 0 || $tenggatOpsi === 'los');

        $tanggalDipilih = $request->query('tanggal', now()->toDateString());
        try {
            $targetDate = Carbon::parse($tanggalDipilih)->startOfDay();
        } catch (\Throwable $e) {
            $targetDate = Carbon::today();
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Batasi tanggal yang dipilih sesuai kebijakan
        if ($targetDate->greaterThan($today)) {
            $targetDate = $today;
        } elseif (!$isModeLos && !$targetDate->equalTo($today)) {
            $targetDate = $today;
        } elseif ($isModeLos && $targetDate->lessThan($yesterday)) {
            $targetDate = $yesterday;
        }

        $namaHari = match ($targetDate->dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };

        // Ambil jadwal mengajar guru pada hari yang dipilih
        $jadwalsHariIni = JadwalPelajaran::with('kelas')
            ->where('id_user', $user?->id)
            ->where('hari', $namaHari)
            ->orderBy('jam_mulai', 'asc')
            ->get();

        // Hitung status ketepatan waktu / keterlambatan tiap jadwal
        $jadwalsWithStatus = $jadwalsHariIni->map(function ($j) use ($currentTime, $targetDate, $today) {
            $mulai = $j->jam_mulai;
            $selesai = $j->jam_selesai;
            $tenggatWaktu = date('H:i:s', strtotime($selesai) + (60 * 60)); // Toleransi 60 menit setelah selesai

            $menitKeterlambatan = 0;
            $statusWaktu = 'Tepat Waktu';

            if (!$targetDate->equalTo($today)) {
                $statusWaktu = 'Susulan (H-1)';
            } elseif ($currentTime < $mulai) {
                $statusWaktu = 'Belum Dimulai';
            } elseif ($currentTime >= $mulai && $currentTime <= $selesai) {
                $diffMinutes = (int) round((strtotime($currentTime) - strtotime($mulai)) / 60);
                if ($diffMinutes > 15) { // Toleransi 15 menit
                    $menitKeterlambatan = $diffMinutes;
                    $statusWaktu = "Terlambat ({$diffMinutes} menit)";
                } else {
                    $statusWaktu = 'Tepat Waktu';
                }
            } else {
                $diffMinutes = (int) round((strtotime($currentTime) - strtotime($mulai)) / 60);
                $menitKeterlambatan = $diffMinutes;
                $statusWaktu = "Terlambat ({$diffMinutes} menit)";
            }

            return [
                'jadwal' => $j,
                'mulai' => substr($mulai, 0, 5),
                'selesai' => substr($selesai, 0, 5),
                'tenggat' => substr($tenggatWaktu, 0, 5),
                'statusWaktu' => $statusWaktu,
                'menitKeterlambatan' => $menitKeterlambatan,
            ];
        });

        $kelases = Kelas::orderBy('nama_kelas')->get();
        $mapels = Mapel::orderBy('nama_mapel')->get();

        // Siswa berdasarkan kelas yang dipilih
        $selectedKelasId = $request->query('kelas_id') ?: optional($jadwalsHariIni->first())->id_kelas ?: optional($kelases->first())->id_kelas;
        $siswas = Siswa::where('kelas_id', $selectedKelasId)->orderBy('nama')->get();

        return view('guru.logbook.create', compact(
            'user',
            'namaHari',
            'currentTime',
            'jadwalsHariIni',
            'jadwalsWithStatus',
            'kelases',
            'mapels',
            'selectedKelasId',
            'siswas',
            'tenggatStatus',
            'tenggatOpsi',
            'isModeLos',
            'targetDate'
        ));
    }

    /**
     * Simpan Pengisian Logbook Jurnal Guru Beserta Keterlambatan & Kebijakan Tenggat Waktu
     */
    public function logbookStore(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_mapel' => 'nullable|exists:mapels,id',
            'tanggal' => 'nullable|date',
            'materi' => 'required|string|max:200',
            'keterangan' => 'nullable|string|max:200',
            'catatan' => 'nullable|string|max:200',
            'jam_ke' => 'nullable|integer',
        ], [
            'id_kelas.required' => 'Pilih kelas mengajar terlebih dahulu.',
            'materi.required' => 'Materi pembelajaran wajib diisi.',
        ]);

        $currentTime = now()->format('H:i:s');
        $tenggatStatus = (int) Pengaturan::getValue('tenggat_status', 1);
        $tenggatOpsi = (string) Pengaturan::getValue('tenggat_opsi', 'terbatas_jam');
        $isModeLos = ($tenggatStatus === 0 || $tenggatOpsi === 'los');

        $inputTanggal = $validated['tanggal'] ?? now()->toDateString();
        try {
            $targetDate = Carbon::parse($inputTanggal)->startOfDay();
        } catch (\Throwable $e) {
            $targetDate = Carbon::today();
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Larangan mutlak: tanggal masa depan
        if ($targetDate->greaterThan($today)) {
            return back()->withInput()->withErrors([
                'tanggal' => 'Pengisian jurnal untuk tanggal di masa depan tidak diperbolehkan.'
            ]);
        }

        // 2. Evaluasi kebijakan tenggat waktu
        if ($isModeLos) {
            // Mode Bebas: maksimal kemarin (H-1)
            if ($targetDate->lessThan($yesterday)) {
                return back()->withInput()->withErrors([
                    'tanggal' => 'Pengisian jurnal susulan maksimal hanya untuk hari kemarin (H-1).'
                ]);
            }
        } else {
            // Mode Tenggat Aktif: wajib hari ini
            if (!$targetDate->equalTo($today)) {
                return back()->withInput()->withErrors([
                    'tanggal' => 'Kebijakan tenggat waktu saat ini hanya mengizinkan pengisian untuk Hari Ini (' . $today->translatedFormat('d F Y') . ').'
                ]);
            }

            // Jika opsi terbatas jam mengajar
            if ($tenggatOpsi === 'terbatas_jam') {
                $namaHariIni = match (now()->dayOfWeek) {
                    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', default => 'Minggu'
                };

                $jadwalSesi = JadwalPelajaran::where('id_user', $user->id)
                    ->where('id_kelas', $validated['id_kelas'])
                    ->where('hari', $namaHariIni)
                    ->first();

                if ($jadwalSesi) {
                    $mulai = $jadwalSesi->jam_mulai;
                    $selesai = $jadwalSesi->jam_selesai;
                    $tenggatSelesai = date('H:i:s', strtotime($selesai) + (60 * 60)); // Toleransi 60 menit

                    if ($currentTime < $mulai) {
                        return back()->withInput()->withErrors([
                            'id_kelas' => "Sesi mengajar kelas ini belum dimulai (mulai {$mulai}). Pengisian jurnal dibatasi pada jam mengajar berlangsung."
                        ]);
                    }

                    if ($currentTime > $tenggatSelesai) {
                        return back()->withInput()->withErrors([
                            'id_kelas' => "Tenggat waktu pengisian sesi ini telah berakhir pukul {$tenggatSelesai} (toleransi 60 menit). Hubungi Admin jika Anda memerlukan izin susulan."
                        ]);
                    }
                }
            }
        }

        $namaHariTarget = match ($targetDate->dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };

        // Cek jadwal mengajar guru pada kelas dan hari target
        $jadwal = JadwalPelajaran::where('id_user', $user->id)
            ->where('id_kelas', $validated['id_kelas'])
            ->where('hari', $namaHariTarget)
            ->first();

        $menitKeterlambatan = 0;
        $statusKeterlambatan = 'Tepat Waktu';

        if ($targetDate->equalTo($today)) {
            if ($jadwal) {
                $mulai = $jadwal->jam_mulai;
                if ($currentTime > $mulai) {
                    $diff = (int) round((strtotime($currentTime) - strtotime($mulai)) / 60);
                    if ($diff > 15) {
                        $menitKeterlambatan = $diff;
                        $statusKeterlambatan = 'Terlambat';
                    }
                }
            } else {
                $statusKeterlambatan = 'Di Luar Jadwal';
            }
        } else {
            $statusKeterlambatan = 'Susulan (H-1)';
        }

        // Simpan jurnal mengajar
        $jurnal = JurnalMengajar::create([
            'id_user' => $user->id,
            'id_kelas' => $validated['id_kelas'],
            'id_mapel' => $validated['id_mapel'] ?? optional($user->mapel)->id ?? optional(Mapel::first())->id ?? 1,
            'tanggal' => $targetDate->toDateString(),
            'jam_ke' => $validated['jam_ke'] ?? optional($jadwal)->jam_ke ?? 1,
            'materi' => $validated['materi'],
            'keterangan' => $validated['keterangan'] ?? "Logbook mandiri guru ({$statusKeterlambatan})",
            'status_kehadiran_guru' => 'Hadir',
            'menit_keterlambatan' => $menitKeterlambatan,
            'status_keterlambatan' => $statusKeterlambatan,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Simpan absensi siswa jika ada
        $absensiData = $request->input('absensi', []);
        if (!empty($absensiData) && is_array($absensiData)) {
            $hadirCount = 0;
            $sakitCount = 0;
            $izinCount = 0;
            $alpaCount = 0;

            foreach ($absensiData as $siswaId => $status) {
                Absensi::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'id_siswa' => $siswaId,
                    'status' => in_array($status, ['Hadir', 'Sakit', 'Izin', 'Alpa', 'Dispensasi']) ? $status : 'Hadir',
                ]);

                if ($status === 'Hadir') $hadirCount++;
                elseif ($status === 'Sakit') $sakitCount++;
                elseif ($status === 'Izin') $izinCount++;
                elseif ($status === 'Alpa') $alpaCount++;
            }

            $jurnal->update([
                'jumlah_hadir' => $hadirCount,
                'jumlah_sakit' => $sakitCount,
                'jumlah_izin' => $izinCount,
                'jumlah_alpa' => $alpaCount,
                'jumlah_tidak_hadir' => ($sakitCount + $izinCount + $alpaCount),
            ]);
        }

        $msg = "Logbook mengajar berhasil disimpan! Status: {$statusKeterlambatan}";
        if ($menitKeterlambatan > 0) {
            $msg .= " (Terlambat {$menitKeterlambatan} menit).";
        }

        return redirect()->back()->with('success', $msg);
    }
}
