<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensis';

    protected $fillable = [
        'id_jurnal',
        'id_siswa',
        'status',
        'catatan',
    ];

    public function jurnal()
    {
        return $this->belongsTo(
            JurnalMengajar::class,
            'id_jurnal',
            'id_jurnal'
        );
    }

    public function siswa()
    {
        return $this->belongsTo(
            Siswa::class,
            'id_siswa',
            'id'
        );
    }
}
