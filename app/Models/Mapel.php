<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'kategori',
        'guru_id',
        'status',
        'alasan_hapus',
    ];

    protected $attributes = [
        'status' => 'aktif',
        'kategori' => 'umum',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function gurus()
    {
        return $this->hasMany(User::class, 'mapel_id');
    }

    public function pengampu()
    {
        return $this->belongsToMany(User::class, 'mapel_user', 'mapel_id', 'user_id');
    }
}
