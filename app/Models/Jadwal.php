<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwals'; // Sesuaikan dengan nama tabel jadwal di database Anda

    protected $guarded = ['id'];

    // Relasi opsional jika diperlukan
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel'); // Sesuaikan foreign key jika ada
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas'); // Sesuaikan foreign key jika ada
    }
}