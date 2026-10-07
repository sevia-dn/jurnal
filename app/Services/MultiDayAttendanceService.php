<?php

namespace App\Services;

use App\Models\PeriodeKetidakhadiranSiswa;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class MultiDayAttendanceService
{
    /**
     * Catat ketidakhadiran multi-hari untuk seorang siswa dan otomatis
     * sinkronkan/buat record PiketKehadiranSiswa untuk setiap hari dalam rentang.
     */
    public function applyMultiDayAttendance(
        Siswa $siswa,
        string $startDate,
        string $endDate,
        string $status,
        ?string $alasan = null,
        ?string $dokumenPath = null,
        ?User $user = null,
        bool $skipWeekends = true,
    ): PeriodeKetidakhadiranSiswa {
        return DB::transaction(function () use (
            $siswa,
            $startDate,
            $endDate,
            $status,
            $alasan,
            $dokumenPath,
            $user,
            $skipWeekends
        ): PeriodeKetidakhadiranSiswa {
            $userId = $user?->id ?? auth()->id();

            $periode = PeriodeKetidakhadiranSiswa::create([
                'siswa_id' => $siswa->id,
                'tanggal_mulai' => $startDate,
                'tanggal_selesai' => $endDate,
                'status' => $status,
                'alasan' => $alasan,
                'dokumen' => $dokumenPath,
                'dicatat_oleh' => $userId,
            ]);

            $dateRange = CarbonPeriod::create($startDate, $endDate);

            foreach ($dateRange as $date) {
                // Lewati akhir pekan (Sabtu & Minggu) jika diaktifkan
                if ($skipWeekends && ($date->isSaturday() || $date->isSunday())) {
                    continue;
                }

                $dateString = $date->toDateString();

                PiketKehadiranSiswa::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'tanggal' => $dateString,
                    ],
                    [
                        'kelas_id' => $siswa->kelas_id,
                        'status' => $status,
                        'sumber' => PiketKehadiranSiswa::SumberGuruPiket,
                        'periode_id' => $periode->id,
                        'is_multi_day' => true,
                        'catatan' => $alasan ? "Multi-hari: {$alasan}" : 'Ketidakhadiran multi-hari',
                        'dicatat_oleh' => $userId,
                    ]
                );
            }

            return $periode;
        });
    }

    /**
     * Hapus periode ketidakhadiran multi-hari beserta record piket kehadiran terkait.
     */
    public function deletePeriod(PeriodeKetidakhadiranSiswa $periode): void
    {
        DB::transaction(function () use ($periode): void {
            PiketKehadiranSiswa::where('periode_id', $periode->id)->delete();
            $periode->delete();
        });
    }
}
