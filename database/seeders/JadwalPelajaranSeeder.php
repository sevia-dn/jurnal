<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jadwal_pelajaran')->insert([
            ['id_jadwal' => 1, 'id_guru' => 1, 'id_kelas' => 1, 'hari' => 'Senin', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00', 'mapel' => 'Basis Data'],
            ['id_jadwal' => 2, 'id_guru' => 2, 'id_kelas' => 2, 'hari' => 'Senin', 'jam_ke' => 2, 'jam_mulai' => '07:45:00', 'jam_selesai' => '08:30:00', 'mapel' => 'Pemrograman Berorientasi Objek'],
            ['id_jadwal' => 3, 'id_guru' => 3, 'id_kelas' => 3, 'hari' => 'Senin', 'jam_ke' => 3, 'jam_mulai' => '08:30:00', 'jam_selesai' => '09:15:00', 'mapel' => 'Pemrograman Web'],
            ['id_jadwal' => 4, 'id_guru' => 4, 'id_kelas' => 1, 'hari' => 'Selasa', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00', 'mapel' => 'Matematika'],
            ['id_jadwal' => 5, 'id_guru' => 5, 'id_kelas' => 2, 'hari' => 'Selasa', 'jam_ke' => 2, 'jam_mulai' => '07:45:00', 'jam_selesai' => '08:30:00', 'mapel' => 'Bahasa Indonesia'],
            ['id_jadwal' => 6, 'id_guru' => 6, 'id_kelas' => 3, 'hari' => 'Rabu', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00', 'mapel' => 'Bahasa Inggris'],
            ['id_jadwal' => 7, 'id_guru' => 7, 'id_kelas' => 7, 'hari' => 'Rabu', 'jam_ke' => 2, 'jam_mulai' => '07:45:00', 'jam_selesai' => '08:30:00', 'mapel' => 'Jaringan Komputer'],
            ['id_jadwal' => 8, 'id_guru' => 8, 'id_kelas' => 8, 'hari' => 'Kamis', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00', 'mapel' => 'PKn'],
            ['id_jadwal' => 9, 'id_guru' => 9, 'id_kelas' => 9, 'hari' => 'Kamis', 'jam_ke' => 2, 'jam_mulai' => '07:45:00', 'jam_selesai' => '08:30:00', 'mapel' => 'Pemrograman Dasar'],
            ['id_jadwal' => 10, 'id_guru' => 10, 'id_kelas' => 10, 'hari' => 'Jumat', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00', 'mapel' => 'Seni Budaya'],
        ]);
    }
}