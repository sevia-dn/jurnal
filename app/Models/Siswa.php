<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswas';

    protected $fillable = [
        'kelas_id',
        'nis',
        'nama',
        'jenis_kelamin',
    ];

    public function kelas()
    {
        return $this->belongsTo(
            Kelas::class,
            'kelas_id',
            'id_kelas'
        );
    }

    public function absensis()
    {
        return $this->hasMany(
            Absensi::class,
            'id_siswa',
            'id'
        );
    }

    public function kehadiranPiket()
    {
        return $this->hasMany(PiketKehadiranSiswa::class, 'siswa_id');
    }
}
