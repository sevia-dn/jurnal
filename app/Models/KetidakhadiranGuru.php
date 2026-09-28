<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KetidakhadiranGuru extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'alasan',
        'keterangan',
        'lampiran',
        'status',
        'handled_by',
        'handled_at',
        'catatan_piket',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'handled_at' => 'datetime',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function getLabelAlasanAttribute(): string
    {
        return match ($this->alasan) {
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            default => ucfirst($this->alasan),
        };
    }
}
