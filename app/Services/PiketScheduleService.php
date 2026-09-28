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
        return JadwalPiket::query()
            ->where('user_id', $user->id)
            ->whereDate('tanggal', $date->toDateString())
            ->exists();
    }

    public function isScheduledNow(User $user): bool
    {
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

        return JadwalPiket::query()
            ->where('user_id', $user->id)
            ->whereDate('tanggal', $now->toDateString())
            ->where('jam_mulai', '<=', $time)
            ->where('jam_selesai', '>', $time)
            ->exists();
    }
}
