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
        'status_konfirmasi_waka',
        'dikonfirmasi_oleh_waka',
        'dikonfirmasi_waka_pada',
        'catatan_waka',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'handled_at' => 'datetime',
        'dikonfirmasi_waka_pada' => 'datetime',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function konfirmatorWaka(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh_waka');
    }

    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isWakaConfirmed(): bool
    {
        return $this->status_konfirmasi_waka === 'dikonfirmasi';
    }

    public function isWakaRejected(): bool
    {
        return $this->status_konfirmasi_waka === 'ditolak';
    }

    public function isWakaPending(): bool
    {
        return $this->status_konfirmasi_waka === 'pending';
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
