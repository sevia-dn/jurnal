<?php

namespace App\Services;

use App\Models\Pengaturan;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class LogbookDeadlinePolicy
{
    /**
     * @return array{mode: string, allows_today: bool, allows_yesterday: bool}
     */
    public function configuration(): array
    {
        $mode = (string) Pengaturan::getValue('tenggat_opsi', 'terbatas_jam');

        if (! in_array($mode, ['terbatas_jam', 'hari_ini', 'los'], true)) {
            $mode = 'terbatas_jam';
        }

        return [
            'mode' => $mode,
            'allows_today' => true,
            'allows_yesterday' => $mode === 'los',
        ];
    }

    public function violation(
        CarbonInterface $submittedAt,
        CarbonInterface $journalDate,
        string $slotStart,
        string $slotEnd,
    ): ?string {
        $configuration = $this->configuration();
        $today = $submittedAt->copy()->setTimezone('Asia/Jakarta')->startOfDay();
        $date = Carbon::parse($journalDate, 'Asia/Jakarta')->startOfDay();

        if ($date->greaterThan($today)) {
            return 'Jurnal untuk tanggal masa depan tidak dapat diisi.';
        }

        if ($configuration['mode'] === 'los') {
            if ($date->lt($today->copy()->subDay())) {
                return 'Mode susulan hanya mengizinkan pengisian jurnal hari ini atau kemarin (H-1).';
            }

            return null;
        }

        if (! $date->isSameDay($today)) {
            return 'Kebijakan saat ini hanya mengizinkan pengisian jurnal untuk hari ini.';
        }

        if ($configuration['mode'] === 'hari_ini') {
            return null;
        }

        $time = $submittedAt->copy()->setTimezone('Asia/Jakarta')->format('H:i');
        if ($time < $slotStart) {
            return "Jam pelajaran untuk sesi ini belum dimulai ({$slotStart} - {$slotEnd} WIB).";
        }

        if ($time > $slotEnd) {
            return "Batas waktu pengisian jurnal untuk sesi ini ({$slotStart} - {$slotEnd} WIB) telah terlewat.";
        }

        return null;
    }
}
