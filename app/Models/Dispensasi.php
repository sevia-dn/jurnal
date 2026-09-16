<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispensasi extends Model
{
    protected $fillable = [
        'siswa_id',
        'jenis_dispensasi',
        'jam_pelajaran',
        'tanggal',
        'tanggal_selesai',
        'alasan',
        'bukti',
        'status_piket',
        'status_waka',
        'status_akhir',
        'token_approval',
        'dibuat_oleh',
        'diproses_oleh',
        'diproses_at',
        'catatan_waka',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_selesai' => 'date',
        'diproses_at' => 'datetime',
    ];

    public function getNamaAttribute()
    {
        return $this->siswa?->nama ?? 'Siswa';
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
