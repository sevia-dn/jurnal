<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswas';

    protected $fillable = [
        'kelas_id',
        'nis',
        'nisn',
        'nama',
        'jenis_kelamin',
    ];

    public function getNisAttribute()
    {
        return $this->attributes['nis'] ?? $this->attributes['nisn'] ?? null;
    }

    public function getNisnAttribute()
    {
        return $this->attributes['nisn'] ?? $this->attributes['nis'] ?? null;
    }

    public function setNisAttribute($value)
    {
        $this->attributes['nis'] = $value;
        if (! isset($this->attributes['nisn']) || empty($this->attributes['nisn'])) {
            $this->attributes['nisn'] = $value;
        }
    }

    public function setNisnAttribute($value)
    {
        $this->attributes['nisn'] = $value;
        if (! isset($this->attributes['nis']) || empty($this->attributes['nis'])) {
            $this->attributes['nis'] = $value;
        }
    }

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
