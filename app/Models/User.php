<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'nip', 'no_hp', 'password', 'role', 'is_waka'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_waka' => 'boolean',
        ];
    }

    public function isWaka(): bool
    {
        return (bool) ($this->is_waka || $this->role === 'waka');
    }

    public function jadwalPikets()
    {
        return $this->hasMany(JadwalPiket::class);
    }

    public function kehadiranGurus()
    {
        return $this->hasMany(KehadiranGuru::class, 'user_id');
    }

    /**
     * Mengecek apakah user memiliki jadwal piket hari ini dan pada shift/jam saat ini
     */
    public function getJadwalPiketAktifAttribute()
    {
        $hariIndo = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $today = $hariIndo[Carbon::now()->format('l')] ?? 'Senin';
        $nowTime = Carbon::now()->format('H:i:s');

        return $this->jadwalPikets()
            ->where('hari', $today)
            ->where('jam_mulai', '<=', $nowTime)
            ->where('jam_selesai', '>=', $nowTime)
            ->first();
    }

    /**
     * Mengecek apakah guru sedang aktif bertugas piket (sudah absen piket hari ini & dalam jam shift piket)
     */
    public function isPiketActive(): bool
    {
        $hariIndo = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $today = $hariIndo[Carbon::now()->format('l')] ?? 'Senin';

        // Cek apakah ada jadwal piket hari ini
        $hasScheduleToday = $this->jadwalPikets()->where('hari', $today)->exists();

        if (! $hasScheduleToday) {
            return false;
        }

        // Cek apakah sudah absen hari ini di KehadiranGuru
        $todayDate = Carbon::now()->toDateString();
        $absenToday = $this->kehadiranGurus()->where('tanggal', $todayDate)->exists();

        // Jika ada jadwal piket hari ini & sudah absen -> Mode piket aktif
        return $absenToday || $hasScheduleToday;
    }
}
