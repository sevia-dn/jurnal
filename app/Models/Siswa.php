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
        'status',
        'alasan_hapus',
    ];

    protected $attributes = [
        'jenis_kelamin' => 'L',
        'status' => 'aktif',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }
}
