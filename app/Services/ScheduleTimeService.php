<?php

namespace App\Services;

use App\Models\Pengaturan;
use Carbon\Carbon;

class ScheduleTimeService
{
    /**
     * @return array{start: string, end: string}
     */
    public function slot(string $hari, int $jamKe): array
    {
        $isJumat = mb_strtolower(trim($hari)) === 'jumat';

        $slots = $isJumat ? $this->jumatSlots() : $this->weekdaySlots();
        $slot = $slots[$jamKe] ?? ($isJumat
            ? ['start' => '07:00', 'end' => '15:35']
            : ['start' => '07:00', 'end' => '15:00']);

        $minutes = $this->advancedMinutes($hari);
        if ($minutes === 0) {
            return $slot;
        }

        return [
            'start' => $this->shiftEarlier($slot['start'], $minutes),
            'end' => $this->shiftEarlier($slot['end'], $minutes),
        ];
    }

    public function advancedMinutes(string $hari): int
    {
        $dayKey = $this->dayKey($hari);
        if ($dayKey === null || ! (bool) Pengaturan::getValue($dayKey.'_is_maju', 0)) {
            return 0;
        }

        $configuredMinutes = $dayKey === 'senin'
            ? (int) Pengaturan::getValue('shift_senin_minutes', 40)
            : (int) Pengaturan::getValue('shift_jumat_minutes', 30);

        $appliedMinutes = (int) Pengaturan::getValue($dayKey.'_shifted_minutes', 0);

        return $appliedMinutes > 0 ? $appliedMinutes : $configuredMinutes;
    }

    /**
     * @return array<int, array{start: string, end: string}>
     */
    private function weekdaySlots(): array
    {
        return [
            1 => ['start' => '07:00', 'end' => '07:40'],
            2 => ['start' => '07:40', 'end' => '08:20'],
            3 => ['start' => '08:20', 'end' => '09:00'],
            4 => ['start' => '09:00', 'end' => '09:40'],
            5 => ['start' => '10:00', 'end' => '10:35'],
            6 => ['start' => '10:35', 'end' => '11:10'],
            7 => ['start' => '11:10', 'end' => '11:45'],
            8 => ['start' => '13:15', 'end' => '13:50'],
            9 => ['start' => '13:50', 'end' => '14:25'],
            10 => ['start' => '14:25', 'end' => '15:00'],
        ];
    }

    /**
     * @return array<int, array{start: string, end: string}>
     */
    private function jumatSlots(): array
    {
        return [
            1 => ['start' => '07:00', 'end' => '07:30'],
            2 => ['start' => '07:30', 'end' => '08:00'],
            3 => ['start' => '08:00', 'end' => '08:30'],
            4 => ['start' => '08:30', 'end' => '09:00'],
            5 => ['start' => '09:00', 'end' => '09:30'],
            6 => ['start' => '09:50', 'end' => '10:20'],
            7 => ['start' => '10:20', 'end' => '10:50'],
            8 => ['start' => '10:50', 'end' => '11:20'],
            9 => ['start' => '13:00', 'end' => '13:30'],
            10 => ['start' => '13:30', 'end' => '14:00'],
            11 => ['start' => '14:00', 'end' => '14:30'],
            12 => ['start' => '14:30', 'end' => '15:10'],
            13 => ['start' => '15:10', 'end' => '15:35'],
        ];
    }

    private function dayKey(string $hari): ?string
    {
        return match (mb_strtolower(trim($hari))) {
            'senin', 'monday' => 'senin',
            'jumat', 'friday' => 'jumat',
            default => null,
        };
    }

    private function shiftEarlier(string $time, int $minutes): string
    {
        return Carbon::createFromFormat('H:i', $time, 'Asia/Jakarta')
            ->subMinutes($minutes)
            ->format('H:i');
    }
}
