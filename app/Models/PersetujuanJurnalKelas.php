<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersetujuanJurnalKelas extends Model
{
    protected $table = 'persetujuan_jurnal_kelas';

    public $timestamps = false;

    protected $fillable = [
        'kelas_id',
        'tanggal',
        'disetujui_oleh',
        'disetujui_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'disetujui_pada' => 'datetime',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function piket(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
