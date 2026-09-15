<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispensasi extends Model
{
    protected $fillable = [
        'siswa_id', 'jenis_dispensasi', 'tanggal', 'tanggal_selesai',
        'alasan', 'bukti', 'status_piket', 'status_waka', 'status_akhir',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
