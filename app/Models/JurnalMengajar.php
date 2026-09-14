<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalMengajar extends Model
{
    use HasFactory;

    protected $table = 'jurnal_mengajars';
    protected $primaryKey = 'id_jurnal';

    protected $fillable = [
        'id_user',
        'id_kelas',
        'id_mapel',
        'tanggal',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'materi',
        'keterangan',
        'jumlah_hadir',
        'jumlah_sakit',
        'jumlah_izin',
        'jumlah_alpa',
        'jumlah_dispensasi',
        'jumlah_tidak_hadir',
        'status_kehadiran_guru',
        'status_validasi',
        'guru_inval_id',
        'ada_tugas',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function guruInval()
    {
        return $this->belongsTo(User::class, 'guru_inval_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'id_jurnal', 'id_jurnal');
    }
}
