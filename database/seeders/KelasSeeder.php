<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kelas')->insert([
            ['id_kelas' => 1, 'nama_kelas' => 'X RPL 1', 'wali_kelas' => 'Sutrisno, S.Kom', 'jumlah_siswa' => 32],
            ['id_kelas' => 2, 'nama_kelas' => 'X RPL 2', 'wali_kelas' => 'Rahmawati, S.Kom', 'jumlah_siswa' => 30],
            ['id_kelas' => 3, 'nama_kelas' => 'XI RPL 1', 'wali_kelas' => 'Yusuf Hidayat, S.Kom', 'jumlah_siswa' => 31],
            ['id_kelas' => 4, 'nama_kelas' => 'XI RPL 2', 'wali_kelas' => 'Dewi Anjani, S.Pd', 'jumlah_siswa' => 29],
            ['id_kelas' => 5, 'nama_kelas' => 'XII RPL 1', 'wali_kelas' => 'Bambang Wijaya, S.Pd', 'jumlah_siswa' => 28],
            ['id_kelas' => 6, 'nama_kelas' => 'XII RPL 2', 'wali_kelas' => 'Siti Nurhaliza, S.Pd', 'jumlah_siswa' => 30],
            ['id_kelas' => 7, 'nama_kelas' => 'X TKJ 1', 'wali_kelas' => 'Agus Setiawan, S.Kom', 'jumlah_siswa' => 33],
            ['id_kelas' => 8, 'nama_kelas' => 'X TKJ 2', 'wali_kelas' => 'Putri Handayani, S.Pd', 'jumlah_siswa' => 31],
            ['id_kelas' => 9, 'nama_kelas' => 'XI TKJ 1', 'wali_kelas' => 'Hendra Gunawan, S.Kom', 'jumlah_siswa' => 30],
            ['id_kelas' => 10, 'nama_kelas' => 'XI TKJ 2', 'wali_kelas' => 'Lestari Wahyuni, S.Pd', 'jumlah_siswa' => 29],
        ]);
    }
}