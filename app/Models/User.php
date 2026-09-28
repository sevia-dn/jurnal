<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\PiketScheduleService;
use App\Services\ScheduleTimeService;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'nip', 'no_hp', 'mapel_id', 'password', 'role', 'is_waka'])]
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
        return (bool) $this->is_waka;
    }

    public function scopeWakaKesiswaan($query)
    {
        return $query->where('is_waka', true);
    }

    public function jadwalPikets()
    {
        return $this->hasMany(JadwalPiket::class);
    }

    public function kehadiranGurus()
    {
        return $this->hasMany(KehadiranGuru::class, 'user_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class, 'id_user');
    }

    /**
     * Mengecek apakah user memiliki jadwal piket hari ini dan pada shift/jam saat ini
     */
    public function getJadwalPiketAktifAttribute()
    {
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $dismissalTime = app(ScheduleTimeService::class)->dismissalTimeForDate($todayDate);
        if ($dismissalTime !== null && $now->format('H:i') >= $dismissalTime) {
            return null;
        }
        $nowTime = $now->format('H:i:s');

        return $this->jadwalPikets()
            ->whereDate('tanggal', $todayDate)
            ->where('jam_mulai', '<=', $nowTime)
            ->where('jam_selesai', '>', $nowTime)
            ->first();
    }

    /**
     * Mengecek status piket berdasarkan tanggal dan rentang shift yang sedang berjalan.
     */
    public function isPiketActive(): bool
    {
        return app(PiketScheduleService::class)->isScheduledNow($this);
    }
}
