<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mapel;

class MapelSeeder extends Seeder {
    public function run() {
        $mapels = [
            ['kode_mapel' => 'MP-PWPB', 'nama_mapel' => 'Pemrograman Web dan Perangkat Bergerak'],
            ['kode_mapel' => 'MP-PBO', 'nama_mapel' => 'Pemrograman Berorientasi Objek'],
            ['kode_mapel' => 'MP-BD', 'nama_mapel' => 'Basis Data (Database)'],
            ['kode_mapel' => 'MP-PPL', 'nama_mapel' => 'Pemodelan Perangkat Lunak'],
            ['kode_mapel' => 'MP-PKK', 'nama_mapel' => 'Produk Kreatif dan Kewirausahaan'],
            ['kode_mapel' => 'MP-ASJ', 'nama_mapel' => 'Administrasi Sistem Jaringan'],
        ];

        foreach ($mapels as $mapel) {
            Mapel::create($mapel);
        }
    }
}