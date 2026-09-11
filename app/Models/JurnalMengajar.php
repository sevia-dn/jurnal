<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalMengajar extends Model
{
    use HasFactory;

    // Matikan timestamp
    public $timestamps = false;

    // WAJIB: Beritahu Laravel bahwa Primary Key tabel ini adalah id_jurnal (bukan 'id')
    protected $primaryKey = 'id_jurnal';

    // WAJIB: Izinkan semua kolom diisi/di-update
    protected $guarded = [];
}