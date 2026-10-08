<?php

namespace App\Services;

use App\Models\JadwalPiket;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class PiketScheduleService
{
    public function isScheduled(User $user, CarbonInterface $date): bool
    {
        if ($this->hasTestModePiketAccess()) {
            return true;
        }

        $dayName = $this->dayName($date);

        return JadwalPiket::query()
            ->where('user_id', $user->id)
            ->where(function ($query) use ($date, $dayName): void {
                $query->whereDate('tanggal', $date->toDateString())
                    ->orWhere(function ($weeklyQuery) use ($dayName): void {
                        $weeklyQuery->whereNull('tanggal')
                            ->where('hari', $dayName);
                    });
            })
            ->exists();
    }

    public function isScheduledNow(User $user): bool
    {
        if ($this->hasTestModePiketAccess()) {
            return true;
        }

        return $this->hasActiveShiftAt($user, now('Asia/Jakarta'));
    }

    public function hasActiveShiftAt(User $user, CarbonInterface $dateTime): bool
    {
        $now = Carbon::instance($dateTime)->setTimezone('Asia/Jakarta');
        $time = $now->format('H:i:s');
        $dismissalTime = app(ScheduleTimeService::class)->dismissalTimeForDate($now->toDateString());

        if ($dismissalTime !== null && $now->format('H:i') >= $dismissalTime) {
            return false;
        }

        $dayName = $this->dayName($now);

        return JadwalPiket::query()
            ->where('user_id', $user->id)
            ->where(function ($query) use ($now, $dayName): void {
                $query->whereDate('tanggal', $now->toDateString())
                    ->orWhere(function ($weeklyQuery) use ($dayName): void {
                        $weeklyQuery->whereNull('tanggal')
                            ->where('hari', $dayName);
                    });
            })
            ->where('jam_mulai', '<=', $time)
            ->where('jam_selesai', '>', $time)
            ->exists();
    }

    private function dayName(CarbonInterface $date): string
    {
        return match ($date->dayOfWeek) {
            Carbon::MONDAY => 'Senin',
            Carbon::TUESDAY => 'Selasa',
            Carbon::WEDNESDAY => 'Rabu',
            Carbon::THURSDAY => 'Kamis',
            Carbon::FRIDAY => 'Jumat',
            Carbon::SATURDAY => 'Sabtu',
            default => 'Minggu',
        };
    }

    private function isTestAccessEnabled(): bool
    {
        return (bool) config('app.piket_test_mode', false);
    }

    private function hasTestModePiketAccess(): bool
    {
        return $this->isTestAccessEnabled();
    }
}
