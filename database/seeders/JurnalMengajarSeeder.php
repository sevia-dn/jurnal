<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurnalMengajarSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jurnal_mengajars')->insert([
            [
                'id_jurnal' => 1,
                'id_user' => 1, 
                'id_kelas' => 1,
                'id_mapel' => 1, 
                'tanggal' => '2026-07-20',
                'jam_ke' => 1,
                'materi' => 'Pengenalan DDL, DML, dan Normalisasi',
                'keterangan' => 'Menerangkan & Praktik',
                'jumlah_hadir' => 31,
                'jumlah_sakit' => 1,
                'jumlah_izin' => 0,
                'jumlah_alpa' => 0,
                'jumlah_dispensasi' => 0,
                'status_kehadiran_guru' => 'Hadir',
                'ada_tugas' => false,
                'catatan' => null,
            ],
            [
                'id_jurnal' => 2,
                'id_user' => 2, 
                'id_kelas' => 1,
                'id_mapel' => 2, 
                'tanggal' => '2026-07-20',
                'jam_ke' => 2,
                'materi' => 'Konsep OOP: Class dan Object',
                'keterangan' => 'Menerangkan & Diskusi',
                'jumlah_hadir' => 29,
                'jumlah_sakit' => 0,
                'jumlah_izin' => 1,
                'jumlah_alpa' => 0,
                'jumlah_dispensasi' => 0,
                'status_kehadiran_guru' => 'Hadir',
                'ada_tugas' => false,
                'catatan' => null,
            ],
            [
                'id_jurnal' => 3,
                'id_user' => 1,
                'id_kelas' => 1,
                'id_mapel' => 3, 
                'tanggal' => '2026-07-21',
                'jam_ke' => 1,
                'materi' => 'Persamaan Linear dan Logika Data',
                'keterangan' => 'Penugasan Mandiri',
                'jumlah_hadir' => 30,
                'jumlah_sakit' => 1,
                'jumlah_izin' => 1,
                'jumlah_alpa' => 0,
                'jumlah_dispensasi' => 0,
                'status_kehadiran_guru' => 'Izin',
                'ada_tugas' => true,
                'catatan' => 'Menghadiri workshop MGMP',
            ],
            [
                'id_jurnal' => 4,
                'id_user' => 2,
                'id_kelas' => 1,
                'id_mapel' => 4, 
                'tanggal' => '2026-07-21',
                'jam_ke' => 2,
                'materi' => 'Pemodelan Perangkat Lunak',
                'keterangan' => 'Tugas Kelompok',
                'jumlah_hadir' => 28,
                'jumlah_sakit' => 0,
                'jumlah_izin' => 0,
                'jumlah_alpa' => 0,
                'jumlah_dispensasi' => 2,
                'status_kehadiran_guru' => 'Sakit',
                'ada_tugas' => true,
                'catatan' => 'Digantikan guru piket, siswa diberi tugas mandiri',
            ],
        ]);
    }
}