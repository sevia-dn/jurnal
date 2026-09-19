<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'username', 'nip', 'password', 'role', 'no_hp', 'mapel_id'])]
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
        ];
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function jadwals()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_user');
    }

    public function jadwalPikets()
    {
        return $this->hasMany(JadwalPiket::class, 'user_id');
    }

    /**
     * Cek apakah user ditugaskan sebagai Guru Piket hari ini
     */
    public function isPiketHariIni(): bool
    {
        $namaHari = match (now()->dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };

        return $this->jadwalPikets()
            ->where('hari', $namaHari)
            ->where('tipe', 'guru')
            ->exists();
    }

    /**
     * Cek apakah user ditugaskan sebagai Waka Piket hari ini
     */
    public function isWakaHariIni(): bool
    {
        $namaHari = match (now()->dayOfWeek) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            default => 'Minggu',
        };

        return $this->jadwalPikets()
            ->where('hari', $namaHari)
            ->where('tipe', 'waka')
            ->exists();
    }
}
