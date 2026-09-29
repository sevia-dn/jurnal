<?php

namespace App\Services;

use App\Models\JadwalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ScheduleSynchronizationService
{
    public function __construct(private ScheduleTimeService $scheduleTimeService) {}

    public function synchronizeAdminSchedules(): int
    {
        return DB::transaction(function (): int {
            $kelasIds = Kelas::query()
                ->where('nama_kelas', 'like', 'X %')
                ->orWhere('nama_kelas', 'like', 'XI %')
                ->pluck('id_kelas');

            JadwalPelajaran::query()
                ->whereIn('id_kelas', $kelasIds)
                ->delete();

            $jadwals = JadwalMengajar::query()
                ->with('mapel')
                ->whereIn('id_kelas', $kelasIds)
                ->orderByRaw("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 ELSE 6 END")
                ->orderBy('jam_mulai')
                ->get();

            $records = $jadwals->map(function (JadwalMengajar $jadwal): array {
                $mulai = $this->scheduleTimeService->slot($jadwal->hari, (int) $jadwal->jam_mulai);
                $selesai = $this->scheduleTimeService->slot($jadwal->hari, (int) $jadwal->jam_selesai);

                return [
                    'id_user' => $jadwal->id_user,
                    'id_kelas' => $jadwal->id_kelas,
                    'id_mapel' => $jadwal->id_mapel,
                    'hari' => $jadwal->hari,
                    'jam_ke' => $jadwal->jam_mulai,
                    'jam_mulai' => $mulai['start'].':00',
                    'jam_selesai' => $selesai['end'].':00',
                    'mapel' => $jadwal->mapel->nama_mapel,
                ];
            })->all();

            $records = array_merge($records, $this->schoolActivityRecords($kelasIds->all()));

            if ($records !== []) {
                JadwalPelajaran::query()->insert($records);
            }

            return count($records);
        });
    }

    /**
     * Tambahkan kegiatan yang berlaku untuk seluruh kelas agar kegiatan tidak
     * hilang setiap jadwal admin diselaraskan dari jadwal mengajar guru.
     *
     * @param  array<int, int>  $kelasIds
     * @return array<int, array<string, int|string|null>>
     */
    private function schoolActivityRecords(array $kelasIds): array
    {
        $systemUserId = User::query()
            ->whereIn('role', ['admin', 'guru'])
            ->orderBy('id')
            ->value('id');

        if ($systemUserId === null) {
            return [];
        }

        $activities = [
            'Upacara / Apel' => ['kode' => 'KGT-UPACARA', 'hari' => ['Senin'], 'mulai' => '07:00:00', 'selesai' => '07:40:00'],
            'Pembiasaan Jumat' => ['kode' => 'KGT-PEMBIASAAN-JUMAT', 'hari' => ['Jumat'], 'mulai' => '07:00:00', 'selesai' => '07:30:00'],
            'Istirahat 1' => ['kode' => 'KGT-ISTIRAHAT-1', 'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'], 'mulai' => null, 'selesai' => null],
            'Istirahat 2' => ['kode' => 'KGT-ISTIRAHAT-2', 'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'], 'mulai' => null, 'selesai' => null],
        ];

        $records = [];
        foreach ($activities as $name => $activity) {
            $mapel = Mapel::query()->firstOrCreate(
                ['kode_mapel' => $activity['kode']],
                ['nama_mapel' => $name, 'kategori' => 'kegiatan']
            );

            foreach ($kelasIds as $kelasId) {
                foreach ($activity['hari'] as $hari) {
                    [$mulai, $selesai] = match ($name) {
                        'Istirahat 1' => $hari === 'Jumat' ? ['09:30:00', '09:50:00'] : ['09:40:00', '10:00:00'],
                        'Istirahat 2' => $hari === 'Jumat' ? ['11:20:00', '13:00:00'] : ['12:00:00', '13:00:00'],
                        default => [$activity['mulai'], $activity['selesai']],
                    };

                    $records[] = [
                        'id_user' => $systemUserId,
                        'id_kelas' => $kelasId,
                        'id_mapel' => $mapel->id,
                        'hari' => $hari,
                        'jam_ke' => 0,
                        'jam_mulai' => $mulai,
                        'jam_selesai' => $selesai,
                        'mapel' => $name,
                    ];
                }
            }
        }

        return $records;
    }
}
