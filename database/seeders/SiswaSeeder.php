<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;

class SiswaSeeder extends Seeder {
    public function run() {
        Siswa::query()->delete();

        $kelasList = Kelas::all();
        if ($kelasList->isEmpty()) {
            return;
        }

        $daftarSiswa = [
            ['nis' => '10101', 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L'],
            ['nis' => '10102', 'nama' => 'Bintang Pratama', 'jenis_kelamin' => 'L'],
            ['nis' => '10103', 'nama' => 'Cika Aurelia', 'jenis_kelamin' => 'P'],
            ['nis' => '10104', 'nama' => 'Dimas Saputra', 'jenis_kelamin' => 'L'],
            ['nis' => '10105', 'nama' => 'Eka Maharani', 'jenis_kelamin' => 'P'],
            ['nis' => '10106', 'nama' => 'Fajar Ramadhan', 'jenis_kelamin' => 'L'],
            ['nis' => '10107', 'nama' => 'Gilang Maulana', 'jenis_kelamin' => 'L'],
            ['nis' => '10108', 'nama' => 'Hanifah Putri', 'jenis_kelamin' => 'P'],
            ['nis' => '10109', 'nama' => 'Indra Lesmana', 'jenis_kelamin' => 'L'],
            ['nis' => '10110', 'nama' => 'Jasmine Azzahra', 'jenis_kelamin' => 'P'],
            ['nis' => '10111', 'nama' => 'Kevin Sanjaya', 'jenis_kelamin' => 'L'],
            ['nis' => '10112', 'nama' => 'Laila Fitriani', 'jenis_kelamin' => 'P'],
        ];

        $kelasCount = $kelasList->count();
        foreach ($daftarSiswa as $index => $siswa) {
            $assignedKelas = $kelasList[$index % $kelasCount];
            Siswa::create([
                'kelas_id' => $assignedKelas->id_kelas,
                'nis' => $siswa['nis'],
                'nama' => $siswa['nama'],
                'jenis_kelamin' => $siswa['jenis_kelamin'],
            ]);
        }
    }
}