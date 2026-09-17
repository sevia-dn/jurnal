<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalMengajar extends Model
{
    protected $table = 'jurnal_mengajars';
    protected $primaryKey = 'id_jurnal';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_kelas',
        'id_mapel',
        'tanggal',
        'jam_ke',
        'materi',
        'keterangan',
        'jumlah_hadir',
        'jumlah_sakit',
        'jumlah_izin',
        'jumlah_alpa',
        'jumlah_dispensasi',
        'jumlah_tidak_hadir',
        'status_kehadiran_guru',
        'ada_tugas',
        'catatan',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_jurnal', 'id_jurnal');
    }
}

