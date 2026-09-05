<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;

class SiswaSeeder extends Seeder {
    public function run() {
        $siswas = [
            ['kelas_id' => 1, 'nis' => '10108', 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L'],
            ['kelas_id' => 1, 'nis' => '10109', 'nama' => 'Bintang Pratama', 'jenis_kelamin' => 'L'],
            ['kelas_id' => 1, 'nis' => '10110', 'nama' => 'Cika Aurelia', 'jenis_kelamin' => 'P'],
            ['kelas_id' => 1, 'nis' => '10111', 'nama' => 'Dimas Saputra', 'jenis_kelamin' => 'L'],
            ['kelas_id' => 1, 'nis' => '10112', 'nama' => 'Eka Maharani', 'jenis_kelamin' => 'P'],
        ];

        foreach ($siswas as $siswa) {
            Siswa::create($siswa);
        }
    }
}