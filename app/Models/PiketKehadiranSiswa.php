<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PiketKehadiranSiswa extends Model
{
    public const SumberGuruPiket = 'guru_piket';

    public const SumberDispensasiWaka = 'dispensasi_waka';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'tanggal',
        'status',
        'sumber',
        'periode_id',
        'is_multi_day',
        'catatan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_multi_day' => 'boolean',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodeKetidakhadiranSiswa::class, 'periode_id');
    }
}
