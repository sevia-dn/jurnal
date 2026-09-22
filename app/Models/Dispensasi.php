<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispensasi extends Model
{
    protected $fillable = [
        'siswa_id',
        'jenis_dispensasi',
        'tipe_dispensasi',
        'jam_ke_mulai',
        'jam_ke_selesai',
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
        'jam_ke_mulai' => 'integer',
        'jam_ke_selesai' => 'integer',
    ];

    public function getNamaAttribute()
    {
        return $this->siswa?->nama ?? 'Siswa';
    }

    /**
     * Accessor untuk deskripsi waktu dispensasi yang manusiawi
     */
    public function getDeskripsiWaktuAttribute(): string
    {
        $tglMulai = $this->tanggal ? $this->tanggal->format('d M Y') : '-';
        $tglSelesai = $this->tanggal_selesai ? $this->tanggal_selesai->format('d M Y') : $tglMulai;

        $jamText = '';
        if ($this->jam_ke_mulai) {
            if ($this->jam_ke_selesai) {
                $jamText = "Jam ke-{$this->jam_ke_mulai} s/d ke-{$this->jam_ke_selesai}";
            } else {
                $jamText = "Jam ke-{$this->jam_ke_mulai} s/d selesai";
            }
        }

        switch ($this->tipe_dispensasi) {
            case 'per_jam':
                return "{$tglMulai}, {$jamText}";

            case 'multi_hari_penuh':
                return "{$tglMulai} s/d {$tglSelesai} (Sepanjang hari)";

            case 'multi_hari_per_jam':
                return "{$tglMulai} s/d {$tglSelesai} ({$jamText} setiap hari)";

            case 'satu_hari':
            default:
                if ($this->jam_ke_mulai) {
                    return "{$tglMulai}, {$jamText}";
                }

                return "{$tglMulai} (Sepanjang hari)";
        }
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
