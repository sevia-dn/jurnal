<?php

namespace App\Console\Commands;

use App\Services\ScheduleSynchronizationService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('jadwal:sync-admin')]
#[Description('Menyelaraskan jadwal Admin kelas X dan XI dari jadwal Guru dan Pengurus Kelas')]
class SyncAdminSchedules extends Command
{
    public function handle(ScheduleSynchronizationService $scheduleSynchronizationService): int
    {
        $total = $scheduleSynchronizationService->synchronizeAdminSchedules();

        $this->info("{$total} jadwal kelas X dan XI berhasil diselaraskan ke Admin.");

        return self::SUCCESS;
    }
}
