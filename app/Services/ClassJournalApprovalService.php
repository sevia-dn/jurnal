<?php

namespace App\Services;

use App\Models\JadwalPelajaran;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\KetidakhadiranGuru;
use App\Models\PersetujuanJurnalKelas;
use App\Models\User;
use Carbon\Carbon;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class ClassJournalApprovalService
{
    public function __construct(private ScheduleTimeService $scheduleTimeService) {}

    /**
     * @param  Collection<int, Kelas>  $classes
     * @return SupportCollection<int, object>
     */
    public function summariesForDate(string $date, Collection $classes): SupportCollection
    {
        if ($classes->isEmpty()) {
            return collect();
        }

        $dayName = $this->dayName($date);
        $classIds = $classes->pluck('id_kelas');
        $schedulesByClass = JadwalPelajaran::with(['guru', 'mapelItem'])
            ->whereIn('id_kelas', $classIds)
            ->where('hari', $dayName)
            ->where('jam_ke', '>', 0)
            ->orderBy('jam_ke')
            ->get()
            ->groupBy('id_kelas');
        $journalsByClass = JurnalMengajar::with(['guru', 'mapel'])
            ->whereIn('id_kelas', $classIds)
            ->whereDate('tanggal', $date)
            ->orderBy('jam_ke')
            ->get()
            ->groupBy('id_kelas');
        $approvalsByClass = PersetujuanJurnalKelas::with('piket')
            ->whereIn('kelas_id', $classIds)
            ->whereDate('tanggal', $date)
            ->get()
            ->keyBy('kelas_id');

        $teacherAbsences = KetidakhadiranGuru::whereDate('tanggal', $date)->get()->keyBy('user_id');

        return $classes->map(function (Kelas $class) use ($approvalsByClass, $date, $journalsByClass, $schedulesByClass, $teacherAbsences): object {
            $schedules = $schedulesByClass->get($class->id_kelas, collect())
                ->filter(fn (JadwalPelajaran $schedule): bool => $this->scheduleTimeService->isScheduleEndApplicableOnDate($date, (string) $schedule->jam_selesai));
            $journals = $journalsByClass->get($class->id_kelas, collect());
            $sessionItems = $schedules->map(function (JadwalPelajaran $schedule) use ($journals, $teacherAbsences, $date): object {
                $journal = $journals->first(function (JurnalMengajar $journal) use ($schedule): bool {
                    return $journal->jam_ke == $schedule->jam_ke
                        || ($schedule->id_mapel && $journal->id_mapel == $schedule->id_mapel && $journal->jam_ke <= $schedule->jam_ke && ($journal->jam_selesai ?? $journal->jam_ke) >= $schedule->jam_ke);
                });

                $teacherAbsence = $schedule->id_user ? $teacherAbsences->get($schedule->id_user) : null;
                $statusInfo = app(JournalStatusService::class)->determineStatus($date, $journal, $teacherAbsence);

                return (object) [
                    'jam_ke' => $schedule->jam_ke,
                    'jam_selesai' => $schedule->jam_ke_selesai ?? $schedule->jam_ke,
                    'jam_ke_formatted' => $schedule->jam_ke_formatted ?? "Jam ke-{$schedule->jam_ke}",
                    'waktu_mulai' => $schedule->jam_mulai,
                    'waktu_selesai' => $schedule->jam_selesai,
                    'mapel' => $schedule->mapelItem?->nama_mapel ?? $schedule->mapel ?? 'Mata Pelajaran',
                    'guru' => $schedule->guru?->name ?? 'Guru Pengampu',
                    'guru_nip' => $schedule->guru?->nip,
                    'is_terisi' => $journal !== null,
                    'is_validated' => $journal?->status_validasi === 'disetujui',
                    'jurnal' => $journal,
                    'status_info' => $statusInfo,
                ];
            });
            $totalSessions = $sessionItems->count();
            $filledSessions = $sessionItems->where('is_terisi', true)->count();
            $validatedSessions = $sessionItems->where('is_validated', true)->count();

            return (object) [
                'id_kelas' => $class->id_kelas,
                'nama_kelas' => $class->nama_kelas,
                'wali_kelas' => $class->wali_kelas ?? '-',
                'total_sesi' => $totalSessions,
                'total_terisi' => $filledSessions,
                'total_kosong' => $totalSessions - $filledSessions,
                'total_tervalidasi' => $validatedSessions,
                'total_menunggu_validasi' => $filledSessions - $validatedSessions,
                'siap_disetujui_piket' => $totalSessions > 0 && $validatedSessions === $totalSessions,
                'persetujuan' => $approvalsByClass->get($class->id_kelas),
                'sesi_items' => $sessionItems,
            ];
        });
    }

    public function approve(Kelas $class, string $date, User $piket): PersetujuanJurnalKelas
    {
        return DB::transaction(function () use ($class, $date, $piket): PersetujuanJurnalKelas {
            $lockedClass = Kelas::query()->lockForUpdate()->findOrFail($class->id_kelas);
            $existingApproval = PersetujuanJurnalKelas::query()
                ->where('kelas_id', $lockedClass->id_kelas)
                ->whereDate('tanggal', $date)
                ->lockForUpdate()
                ->first();

            if ($existingApproval !== null) {
                return $existingApproval;
            }

            $summary = $this->summariesForDate($date, new Collection([$lockedClass]))->first();
            if (! $summary?->siap_disetujui_piket) {
                throw new DomainException('Jurnal kelas belum lengkap atau masih menunggu validasi pengurus kelas.');
            }

            return PersetujuanJurnalKelas::create([
                'kelas_id' => $lockedClass->id_kelas,
                'tanggal' => $date,
                'disetujui_oleh' => $piket->id,
                'disetujui_pada' => now('Asia/Jakarta'),
            ]);
        });
    }

    private function dayName(string $date): string
    {
        return match (Carbon::parse($date)->dayOfWeek) {
            Carbon::MONDAY => 'Senin',
            Carbon::TUESDAY => 'Selasa',
            Carbon::WEDNESDAY => 'Rabu',
            Carbon::THURSDAY => 'Kamis',
            Carbon::FRIDAY => 'Jumat',
            Carbon::SATURDAY => 'Sabtu',
            default => 'Minggu',
        };
    }
}
