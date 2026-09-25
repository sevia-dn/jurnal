<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PiketKehadiranSiswa extends Model
{
    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'tanggal',
        'status',
        'catatan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
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
}
