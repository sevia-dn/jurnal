<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajarans';

    protected $primaryKey = 'id_jadwal';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_kelas',
        'id_mapel',
        'hari',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'mapel',
    ];
}
