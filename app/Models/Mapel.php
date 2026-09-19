<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kategori',
    ];

    public function gurus()
    {
        return $this->hasMany(User::class, 'mapel_id');
    }

    public function guru()
    {
        return $this->hasMany(User::class, 'mapel_id');
    }

    public function jadwals()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_mapel');
    }
}
