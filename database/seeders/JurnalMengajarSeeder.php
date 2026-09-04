<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurnalMengajarSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jurnal_mengajars')->insert([
            ['id_jurnal' => 1, 'id_guru' => 1, 'id_kelas' => 1, 'tanggal' => '2026-07-20', 'jam_ke' => 1, 'materi' => 'Pengenalan DDL, DML, dan Normalisasi', 'jumlah_hadir' => 31, 'jumlah_tidak_hadir' => 1, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 2, 'id_guru' => 2, 'id_kelas' => 2, 'tanggal' => '2026-07-20', 'jam_ke' => 2, 'materi' => 'Konsep OOP: Class dan Object', 'jumlah_hadir' => 29, 'jumlah_tidak_hadir' => 1, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 3, 'id_guru' => 3, 'id_kelas' => 3, 'tanggal' => '2026-07-20', 'jam_ke' => 3, 'materi' => 'Dasar HTML & CSS', 'jumlah_hadir' => 30, 'jumlah_tidak_hadir' => 1, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 4, 'id_guru' => 1, 'id_kelas' => 4, 'tanggal' => '2026-07-20', 'jam_ke' => 4, 'materi' => 'Normalisasi Basis Data', 'jumlah_hadir' => 28, 'jumlah_tidak_hadir' => 1, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 5, 'id_guru' => 4, 'id_kelas' => 1, 'tanggal' => '2026-07-21', 'jam_ke' => 1, 'materi' => 'Persamaan Linear', 'jumlah_hadir' => 30, 'jumlah_tidak_hadir' => 2, 'status_kehadiran_guru' => 'Izin', 'catatan' => 'Menghadiri workshop MGMP'],
            ['id_jurnal' => 6, 'id_guru' => 5, 'id_kelas' => 2, 'tanggal' => '2026-07-21', 'jam_ke' => 2, 'materi' => 'Teks Eksposisi', 'jumlah_hadir' => null, 'jumlah_tidak_hadir' => null, 'status_kehadiran_guru' => 'Sakit', 'catatan' => 'Digantikan guru piket, siswa diberi tugas mandiri'],
            ['id_jurnal' => 7, 'id_guru' => 6, 'id_kelas' => 3, 'tanggal' => '2026-07-21', 'jam_ke' => 3, 'materi' => 'Simple Present Tense', 'jumlah_hadir' => 31, 'jumlah_tidak_hadir' => 0, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 8, 'id_guru' => 7, 'id_kelas' => 7, 'tanggal' => '2026-07-21', 'jam_ke' => 4, 'materi' => 'Topologi Jaringan', 'jumlah_hadir' => 32, 'jumlah_tidak_hadir' => 1, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 9, 'id_guru' => 2, 'id_kelas' => 5, 'tanggal' => '2026-07-22', 'jam_ke' => 1, 'materi' => 'Inheritance dan Polymorphism', 'jumlah_hadir' => 27, 'jumlah_tidak_hadir' => 1, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 10, 'id_guru' => 3, 'id_kelas' => 6, 'tanggal' => '2026-07-22', 'jam_ke' => 2, 'materi' => 'JavaScript Dasar', 'jumlah_hadir' => 29, 'jumlah_tidak_hadir' => 1, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 11, 'id_guru' => 8, 'id_kelas' => 8, 'tanggal' => '2026-07-22', 'jam_ke' => 3, 'materi' => 'Hak dan Kewajiban Warga Negara', 'jumlah_hadir' => null, 'jumlah_tidak_hadir' => null, 'status_kehadiran_guru' => 'Tanpa Keterangan', 'catatan' => 'Guru piket mengisi jam kosong'],
            ['id_jurnal' => 12, 'id_guru' => 9, 'id_kelas' => 9, 'tanggal' => '2026-07-22', 'jam_ke' => 4, 'materi' => 'Struktur Data Array', 'jumlah_hadir' => 30, 'jumlah_tidak_hadir' => 0, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 13, 'id_guru' => 1, 'id_kelas' => 3, 'tanggal' => '2026-07-23', 'jam_ke' => 1, 'materi' => 'Query DML Lanjutan', 'jumlah_hadir' => 28, 'jumlah_tidak_hadir' => 3, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 14, 'id_guru' => 10, 'id_kelas' => 10, 'tanggal' => '2026-07-23', 'jam_ke' => 2, 'materi' => 'Apresiasi Seni Rupa', 'jumlah_hadir' => 29, 'jumlah_tidak_hadir' => 0, 'status_kehadiran_guru' => 'Hadir', 'catatan' => null],
            ['id_jurnal' => 15, 'id_guru' => 5, 'id_kelas' => 4, 'tanggal' => '2026-07-23', 'jam_ke' => 3, 'materi' => 'Teks Deskripsi', 'jumlah_hadir' => 26, 'jumlah_tidak_hadir' => 3, 'status_kehadiran_guru' => 'Izin', 'catatan' => 'Rapat wali kelas'],
        ]);
    }
}