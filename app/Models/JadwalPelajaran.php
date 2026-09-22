<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'status',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function mapelItem(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'id_mapel');
    }
}
