<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengaturanJurnal extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_jurnals';

    protected $fillable = [
        'kebijakan_tenggat',
        'diubah_oleh',
    ];

    /**
     * Mendapatkan kebijakan tenggat yang aktif saat ini.
     * Menggunakan singleton row (id = 1).
     */
    public static function getKebijakanAktif(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            ['kebijakan_tenggat' => 'jam_mengajar']
        );
    }

    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
