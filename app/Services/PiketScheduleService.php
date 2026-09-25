<?php

namespace App\Services;

use App\Models\JadwalPiket;
use App\Models\User;
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
        return $this->isScheduled($user, now('Asia/Jakarta'));
    }
}
