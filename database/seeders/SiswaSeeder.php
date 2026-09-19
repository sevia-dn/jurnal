<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;

class SiswaSeeder extends Seeder {
    public function run() {
        $siswas = [
            ['kelas_id' => 1, 'nisn' => '0101081001', 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L'],
            ['kelas_id' => 1, 'nisn' => '0101081002', 'nama' => 'Bintang Pratama', 'jenis_kelamin' => 'L'],
            ['kelas_id' => 1, 'nisn' => '0101081003', 'nama' => 'Cika Aurelia', 'jenis_kelamin' => 'P'],
            ['kelas_id' => 1, 'nisn' => '0101081004', 'nama' => 'Dimas Saputra', 'jenis_kelamin' => 'L'],
            ['kelas_id' => 1, 'nisn' => '0101081005', 'nama' => 'Eka Maharani', 'jenis_kelamin' => 'P'],
        ];

        foreach ($siswas as $siswa) {
            Siswa::create($siswa);
        }
    }
}