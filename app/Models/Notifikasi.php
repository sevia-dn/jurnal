<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'id_user',
        'id_kelas',
        'id_dispensasi',
        'id_jurnal',
        'id_ketidakhadiran_guru',
        'judul',
        'pesan',
        'tipe',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function dispensasi()
    {
        return $this->belongsTo(Dispensasi::class, 'id_dispensasi');
    }

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(JurnalMengajar::class, 'id_jurnal', 'id_jurnal');
    }

    public function ketidakhadiranGuru(): BelongsTo
    {
        return $this->belongsTo(KetidakhadiranGuru::class, 'id_ketidakhadiran_guru');
    }
}
