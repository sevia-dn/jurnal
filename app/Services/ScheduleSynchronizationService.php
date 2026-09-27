<?php

namespace App\Services;

use App\Models\JadwalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
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

            if ($records !== []) {
                JadwalPelajaran::query()->insert($records);
            }

            return count($records);
        });
    }
}
