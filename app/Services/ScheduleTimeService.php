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
            ? ['start' => '07:00', 'end' => '15:30']
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

    public function dismissalTimeForDate(string $date): ?string
    {
        if ((string) Pengaturan::getValue('event_sekolah_tanggal', '') !== $date) {
            return null;
        }

        $dismissalTime = trim((string) Pengaturan::getValue('event_sekolah_jam_pulang', ''));

        return $dismissalTime !== '' ? substr($dismissalTime, 0, 5) : null;
    }

    /**
     * Cek apakah tanggal termasuk dalam rentang jam kosong seharian
     * (misal saat classmeet / event yang tidak perlu isi jurnal).
     */
    public function isAllDayEmptyForDate(string $date): bool
    {
        $nama = trim((string) Pengaturan::getValue('jam_kosong_nama', ''));
        if ($nama === '') {
            return false;
        }

        $tanggalMulai = trim((string) Pengaturan::getValue('jam_kosong_tanggal_mulai', ''));
        $tanggalSelesai = trim((string) Pengaturan::getValue('jam_kosong_tanggal_selesai', ''));

        if ($tanggalMulai === '' || $tanggalSelesai === '') {
            return false;
        }

        return $date >= $tanggalMulai && $date <= $tanggalSelesai;
    }

    public function isSessionApplicableOnDate(string $date, string $hari, int $jamKe): bool
    {
        if ($this->isAllDayEmptyForDate($date)) {
            return false;
        }

        $dismissalTime = $this->dismissalTimeForDate($date);
        if ($dismissalTime === null) {
            return true;
        }

        $slot = $this->slot($hari, $jamKe);

        return $slot['end'] <= $dismissalTime;
    }

    public function isScheduleEndApplicableOnDate(string $date, string $endTime): bool
    {
        if ($this->isAllDayEmptyForDate($date)) {
            return false;
        }

        $dismissalTime = $this->dismissalTimeForDate($date);
        if ($dismissalTime === null) {
            return true;
        }

        return substr(trim($endTime), 0, 5) <= $dismissalTime;
    }

    public function isLessonRangeApplicableOnDate(string $date, string $hari, int $jamMulai, int $jamSelesai): bool
    {
        for ($jamKe = $jamMulai; $jamKe <= $jamSelesai; $jamKe++) {
            if (! $this->isSessionApplicableOnDate($date, $hari, $jamKe)) {
                return false;
            }
        }

        return true;
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
    public function weekdaySlots(): array
    {
        return [
            1 => ['start' => '07:00', 'end' => '07:40'],
            2 => ['start' => '07:40', 'end' => '08:20'],
            3 => ['start' => '08:20', 'end' => '09:00'],
            4 => ['start' => '09:00', 'end' => '09:40'],
            5 => ['start' => '10:00', 'end' => '10:40'],
            6 => ['start' => '10:40', 'end' => '11:20'],
            7 => ['start' => '11:20', 'end' => '12:00'],
            8 => ['start' => '13:00', 'end' => '13:40'],
            9 => ['start' => '13:40', 'end' => '14:20'],
            10 => ['start' => '14:20', 'end' => '15:00'],
        ];
    }

    /**
     * @return array<int, array{start: string, end: string}>
     */
    public function jumatSlots(): array
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
            12 => ['start' => '14:30', 'end' => '15:00'],
            13 => ['start' => '15:00', 'end' => '15:30'],
        ];
    }

    /**
     * Mendapatkan nomor jam ke (slot) berdasarkan waktu jam selesai.
     */
    public function slotNumberFromEndTime(string $hari, string $endTime): ?int
    {
        $isJumat = mb_strtolower(trim($hari)) === 'jumat';
        $slots = $isJumat ? $this->jumatSlots() : $this->weekdaySlots();
        $minutes = $this->advancedMinutes($hari);
        $normalizedEnd = substr(trim($endTime), 0, 5);

        foreach ($slots as $num => $slot) {
            $slotEnd = $minutes > 0 ? $this->shiftEarlier($slot['end'], $minutes) : $slot['end'];
            if ($slotEnd === $normalizedEnd) {
                return $num;
            }
        }

        return null;
    }

    /**
     * Format tampilan rentang jam ke, misal "Jam Ke-2 s/d 4" atau "Jam Ke-1".
     */
    public function formatJamKeRange(string $hari, int $jamKeMulai, ?string $jamSelesaiTime = null, ?int $jamKeSelesai = null): string
    {
        if ($jamKeMulai <= 0) {
            return 'Kegiatan Khusus';
        }

        $endSlot = $jamKeSelesai;
        if ($endSlot === null && $jamSelesaiTime !== null) {
            $endSlot = $this->slotNumberFromEndTime($hari, $jamSelesaiTime);
        }

        if ($endSlot !== null && $endSlot > $jamKeMulai) {
            return "Jam Ke-{$jamKeMulai} s/d {$endSlot}";
        }

        return "Jam Ke-{$jamKeMulai}";
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
