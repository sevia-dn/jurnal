<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mapel;

class MapelSeeder extends Seeder {
    public function run() {
        $jsonPath = base_path('scripts/master_data.json');
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
            $mapels = $data['mapels'] ?? [];
            foreach ($mapels as $mapel) {
                Mapel::updateOrCreate(
                    ['kode_mapel' => $mapel['kode']],
                    [
                        'nama_mapel' => $mapel['nama'],
                        'kategori' => $mapel['kategori'] ?? 'biasa'
                    ]
                );
            }
        } else {
            $mapels = [
                ['kode_mapel' => 'MP-PWPB', 'nama_mapel' => 'Pemrograman Web dan Perangkat Bergerak', 'kategori' => 'jurusan'],
                ['kode_mapel' => 'MP-PBO', 'nama_mapel' => 'Pemrograman Berorientasi Objek', 'kategori' => 'jurusan'],
                ['kode_mapel' => 'MP-BD', 'nama_mapel' => 'Basis Data (Database)', 'kategori' => 'jurusan'],
                ['kode_mapel' => 'MP-PPL', 'nama_mapel' => 'Pemodelan Perangkat Lunak', 'kategori' => 'jurusan'],
                ['kode_mapel' => 'MP-PKK', 'nama_mapel' => 'Produk Kreatif dan Kewirausahaan', 'kategori' => 'biasa'],
                ['kode_mapel' => 'MP-ASJ', 'nama_mapel' => 'Administrasi Sistem Jaringan', 'kategori' => 'jurusan'],
            ];

            foreach ($mapels as $mapel) {
                Mapel::updateOrCreate(['kode_mapel' => $mapel['kode_mapel']], $mapel);
            }
        }
    }
}