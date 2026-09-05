<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalPelajaranSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('jadwal_pelajarans')->insert([
            // --- SENIN ---
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => null, 'hari' => 'Senin', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00', 'mapel' => 'Upacara / Apel'],
            ['id_user' => 2, 'id_kelas' => 1, 'id_mapel' => 5,    'hari' => 'Senin', 'jam_ke' => 2, 'jam_mulai' => '07:45:00', 'jam_selesai' => '10:00:00', 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan'],
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Senin', 'jam_ke' => 5, 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:45:00', 'mapel' => 'Bahasa Inggris'],
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Senin', 'jam_ke' => 7, 'jam_mulai' => '12:30:00', 'jam_selesai' => '15:30:00', 'mapel' => 'Konsentrasi RPL'],

            // --- SELASA ---
            ['id_user' => 2, 'id_kelas' => 1, 'id_mapel' => 2,    'hari' => 'Selasa', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '09:15:00', 'mapel' => 'Matematika'],
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Selasa', 'jam_ke' => 4, 'jam_mulai' => '09:15:00', 'jam_selesai' => '10:45:00', 'mapel' => 'Bahasa Inggris'],
            ['id_user' => 2, 'id_kelas' => 1, 'id_mapel' => 2,    'hari' => 'Selasa', 'jam_ke' => 6, 'jam_mulai' => '11:00:00', 'jam_selesai' => '12:30:00', 'mapel' => 'PJOK'],
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Selasa', 'jam_ke' => 8, 'jam_mulai' => '13:00:00', 'jam_selesai' => '15:15:00', 'mapel' => 'Konsentrasi RPL'],

            // --- RABU ---
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Rabu', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '10:00:00', 'mapel' => 'Konsentrasi RPL'],
            ['id_user' => 2, 'id_kelas' => 1, 'id_mapel' => 2,    'hari' => 'Rabu', 'jam_ke' => 5, 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:45:00', 'mapel' => 'Mapel Pilihan RPL'],
            ['id_user' => 2, 'id_kelas' => 1, 'id_mapel' => 2,    'hari' => 'Rabu', 'jam_ke' => 7, 'jam_mulai' => '12:30:00', 'jam_selesai' => '14:00:00', 'mapel' => 'Pendidikan Pancasila'],
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Rabu', 'jam_ke' => 9, 'jam_mulai' => '14:00:00', 'jam_selesai' => '15:30:00', 'mapel' => 'Sejarah'],

            // --- KAMIS ---
            ['id_user' => 2, 'id_kelas' => 1, 'id_mapel' => 2,    'hari' => 'Kamis', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '08:30:00', 'mapel' => 'Bahasa Jepang'],
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Kamis', 'jam_ke' => 3, 'jam_mulai' => '08:30:00', 'jam_selesai' => '10:00:00', 'mapel' => 'Bimbingan Konseling (BK)'],
            ['id_user' => 2, 'id_kelas' => 1, 'id_mapel' => 2,    'hari' => 'Kamis', 'jam_ke' => 5, 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:45:00', 'mapel' => 'Bahasa Jawa'],
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Kamis', 'jam_ke' => 7, 'jam_mulai' => '12:30:00', 'jam_selesai' => '15:30:00', 'mapel' => 'Konsentrasi RPL'],

            // --- JUMAT ---
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => null, 'hari' => 'Jumat', 'jam_ke' => 1, 'jam_mulai' => '07:00:00', 'jam_selesai' => '07:45:00', 'mapel' => 'Pembiasaan Hari Jumat'],
            ['id_user' => 2, 'id_kelas' => 1, 'id_mapel' => 2,    'hari' => 'Jumat', 'jam_ke' => 2, 'jam_mulai' => '07:45:00', 'jam_selesai' => '10:00:00', 'mapel' => 'Bahasa Indonesia'],
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Jumat', 'jam_ke' => 5, 'jam_mulai' => '10:15:00', 'jam_selesai' => '11:45:00', 'mapel' => 'Pendidikan Agama Islam'],
            ['id_user' => 1, 'id_kelas' => 1, 'id_mapel' => 1,    'hari' => 'Jumat', 'jam_ke' => 8, 'jam_mulai' => '13:00:00', 'jam_selesai' => '15:15:00', 'mapel' => 'Konsentrasi RPL'],
            ['id_user' => 2, 'id_kelas' => 1, 'id_mapel' => 5,    'hari' => 'Jumat', 'jam_ke' => 11, 'jam_mulai' => '15:15:00', 'jam_selesai' => '16:45:00', 'mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan'],
        ]);
    }
}