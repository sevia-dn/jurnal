<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = [
            ['id_kelas' => 1, 'nama_kelas' => 'X TKI 1'],
            ['id_kelas' => 2, 'nama_kelas' => 'X TKI 2'],
            ['id_kelas' => 3, 'nama_kelas' => 'X RPL 1'],
            ['id_kelas' => 4, 'nama_kelas' => 'X RPL 2'],
            ['id_kelas' => 5, 'nama_kelas' => 'X TKJ 1'],
            ['id_kelas' => 6, 'nama_kelas' => 'X TKJ 2'],
            ['id_kelas' => 7, 'nama_kelas' => 'X BD 1'],
            ['id_kelas' => 8, 'nama_kelas' => 'X BD 2'],
            ['id_kelas' => 9, 'nama_kelas' => 'X BD 3'],
            ['id_kelas' => 10, 'nama_kelas' => 'X MP 1'],
            ['id_kelas' => 11, 'nama_kelas' => 'X MP 2'],
            ['id_kelas' => 12, 'nama_kelas' => 'X MP 3'],
            ['id_kelas' => 13, 'nama_kelas' => 'X MP 4'],
            ['id_kelas' => 14, 'nama_kelas' => 'X AK 1'],
            ['id_kelas' => 15, 'nama_kelas' => 'X AK 2'],
            ['id_kelas' => 16, 'nama_kelas' => 'X AK 3'],
            ['id_kelas' => 17, 'nama_kelas' => 'X AK 4'],
            ['id_kelas' => 18, 'nama_kelas' => 'X ULW'],
            ['id_kelas' => 19, 'nama_kelas' => 'X DKV 1'],
            ['id_kelas' => 20, 'nama_kelas' => 'X DKV 2'],
            ['id_kelas' => 21, 'nama_kelas' => 'X PSPT 1'],
            ['id_kelas' => 22, 'nama_kelas' => 'X PSPT 2'],
            ['id_kelas' => 23, 'nama_kelas' => 'X AN 1'],
            ['id_kelas' => 24, 'nama_kelas' => 'X AN 2'],

            ['id_kelas' => 25, 'nama_kelas' => 'XI TKI 1'],
            ['id_kelas' => 26, 'nama_kelas' => 'XI TKI 2'],
            ['id_kelas' => 27, 'nama_kelas' => 'XI RPL 1'],
            ['id_kelas' => 28, 'nama_kelas' => 'XI RPL 2'],
            ['id_kelas' => 29, 'nama_kelas' => 'XI TKJ 1'],
            ['id_kelas' => 30, 'nama_kelas' => 'XI TKJ 2'],
            ['id_kelas' => 31, 'nama_kelas' => 'XI BD 1'],
            ['id_kelas' => 32, 'nama_kelas' => 'XI BD 2'],
            ['id_kelas' => 33, 'nama_kelas' => 'XI BD 3'],
            ['id_kelas' => 34, 'nama_kelas' => 'XI MP 1'],
            ['id_kelas' => 35, 'nama_kelas' => 'XI MP 2'],
            ['id_kelas' => 36, 'nama_kelas' => 'XI MP 3'],
            ['id_kelas' => 37, 'nama_kelas' => 'XI MP 4'],
            ['id_kelas' => 38, 'nama_kelas' => 'XI AK 1'],
            ['id_kelas' => 39, 'nama_kelas' => 'XI AK 2'],
            ['id_kelas' => 40, 'nama_kelas' => 'XI AK 3'],
            ['id_kelas' => 41, 'nama_kelas' => 'XI AK 4'],
            ['id_kelas' => 42, 'nama_kelas' => 'XI ULW'],
            ['id_kelas' => 43, 'nama_kelas' => 'XI DKV 1'],
            ['id_kelas' => 44, 'nama_kelas' => 'XI DKV 2'],
            ['id_kelas' => 45, 'nama_kelas' => 'XI PSPT 1'],
            ['id_kelas' => 46, 'nama_kelas' => 'XI PSPT 2'],
            ['id_kelas' => 47, 'nama_kelas' => 'XI AN 1'],
            ['id_kelas' => 48, 'nama_kelas' => 'XI AN 2'],
        ];

        foreach ($kelas as $item) {
            Kelas::updateOrCreate(
                ['id_kelas' => $item['id_kelas']],
                [
                    'nama_kelas' => $item['nama_kelas'],
                    'jumlah_siswa' => 0,
                ]
            );
        }
    }
}
