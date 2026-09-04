<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('gurus')->insert([
            ['id_guru' => 1, 'nip' => '198501012010011001', 'nama_guru' => 'Sutrisno, S.Kom', 'mapel_diampu' => 'Basis Data', 'no_hp' => '081234500001', 'status_kepegawaian' => 'PNS'],
            ['id_guru' => 2, 'nip' => '198702152011012002', 'nama_guru' => 'Rahmawati, S.Kom', 'mapel_diampu' => 'Pemrograman Berorientasi Objek', 'no_hp' => '081234500002', 'status_kepegawaian' => 'PNS'],
            ['id_guru' => 3, 'nip' => '199001102015011003', 'nama_guru' => 'Yusuf Hidayat, S.Kom', 'mapel_diampu' => 'Pemrograman Web', 'no_hp' => '081234500003', 'status_kepegawaian' => 'PPPK'],
            ['id_guru' => 4, 'nip' => '199203202016012004', 'nama_guru' => 'Dewi Anjani, S.Pd', 'mapel_diampu' => 'Matematika', 'no_hp' => '081234500004', 'status_kepegawaian' => 'PNS'],
            ['id_guru' => 5, 'nip' => '198809302012011005', 'nama_guru' => 'Bambang Wijaya, S.Pd', 'mapel_diampu' => 'Bahasa Indonesia', 'no_hp' => '081234500005', 'status_kepegawaian' => 'Honorer'],
            ['id_guru' => 6, 'nip' => '199105182017012006', 'nama_guru' => 'Siti Nurhaliza, S.Pd', 'mapel_diampu' => 'Bahasa Inggris', 'no_hp' => '081234500006', 'status_kepegawaian' => 'PPPK'],
            ['id_guru' => 7, 'nip' => '198712252013011007', 'nama_guru' => 'Agus Setiawan, S.Kom', 'mapel_diampu' => 'Jaringan Komputer', 'no_hp' => '081234500007', 'status_kepegawaian' => 'PNS'],
            ['id_guru' => 8, 'nip' => '199304142018012008', 'nama_guru' => 'Putri Handayani, S.Pd', 'mapel_diampu' => 'PKn', 'no_hp' => '081234500008', 'status_kepegawaian' => 'Honorer'],
            ['id_guru' => 9, 'nip' => '198611052012011009', 'nama_guru' => 'Hendra Gunawan, S.Kom', 'mapel_diampu' => 'Pemrograman Dasar', 'no_hp' => '081234500009', 'status_kepegawaian' => 'PNS'],
            ['id_guru' => 10, 'nip' => '199206302019012010', 'nama_guru' => 'Lestari Wahyuni, S.Pd', 'mapel_diampu' => 'Seni Budaya', 'no_hp' => '081234500010', 'status_kepegawaian' => 'Honorer'],
        ]);
    }
}