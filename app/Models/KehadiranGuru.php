<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KehadiranGuru extends Model
{
    protected $fillable = ['user_id', 'tanggal', 'jam_masuk', 'status', 'diverifikasi_oleh', 'diverifikasi_at'];

    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}