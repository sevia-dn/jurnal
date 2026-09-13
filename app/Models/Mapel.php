<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $fillable = ['kode_mapel', 'nama_mapel'];

    /**
     * Relasi: satu mapel bisa diajar banyak guru (user).
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}