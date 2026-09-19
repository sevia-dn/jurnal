<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswas';

    protected $fillable = [
        'kelas_id',
        'nisn',
        'nis',
        'nama',
        'jenis_kelamin',
    ];

    public function getNisAttribute()
    {
        return $this->attributes['nisn'] ?? $this->attributes['nis'] ?? null;
    }

    public function setNisAttribute($value)
    {
        $this->attributes['nisn'] = $value;
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }
}
