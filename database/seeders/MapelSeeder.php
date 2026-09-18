<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mapel;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapels = [
            ['kode_mapel' => 'MP-BINDO', 'nama_mapel' => 'Bahasa Indonesia'],
            ['kode_mapel' => 'MP-BING', 'nama_mapel' => 'Bahasa Inggris'],
            ['kode_mapel' => 'MP-BJAWA', 'nama_mapel' => 'Bahasa Jawa'],
            ['kode_mapel' => 'MP-BJEPANG', 'nama_mapel' => 'Bahasa Jepang'],
            ['kode_mapel' => 'MP-BK', 'nama_mapel' => 'BK'],

            ['kode_mapel' => 'MP-DASAR-AKL', 'nama_mapel' => 'Dasar AKL'],
            ['kode_mapel' => 'MP-DASAR-AN', 'nama_mapel' => 'Dasar AN'],
            ['kode_mapel' => 'MP-DASAR-BP', 'nama_mapel' => 'Dasar BP'],
            ['kode_mapel' => 'MP-DASAR-DKV', 'nama_mapel' => 'Dasar DKV'],
            ['kode_mapel' => 'MP-DASAR-MPLB', 'nama_mapel' => 'Dasar MPLB'],
            ['kode_mapel' => 'MP-DASAR-PM', 'nama_mapel' => 'Dasar PM'],
            ['kode_mapel' => 'MP-DASAR-PPLG', 'nama_mapel' => 'Dasar PPLG'],
            ['kode_mapel' => 'MP-DASAR-TJKT', 'nama_mapel' => 'Dasar TJKT'],
            ['kode_mapel' => 'MP-DASAR-TKI', 'nama_mapel' => 'Dasar TKI'],
            ['kode_mapel' => 'MP-DASAR-ULP', 'nama_mapel' => 'Dasar ULP'],

            ['kode_mapel' => 'MP-IPAS', 'nama_mapel' => 'IPAS'],
            ['kode_mapel' => 'MP-INFORMATIKA', 'nama_mapel' => 'Informatika'],
            ['kode_mapel' => 'MP-KKA', 'nama_mapel' => 'Koding dan Kecerdasan Artifisial'],

            ['kode_mapel' => 'MP-KONS-AKL', 'nama_mapel' => 'Konsentrasi AK'],
            ['kode_mapel' => 'MP-KONS-AN', 'nama_mapel' => 'Konsentrasi AN'],
            ['kode_mapel' => 'MP-KONS-BD', 'nama_mapel' => 'Konsentrasi BD'],
            ['kode_mapel' => 'MP-KONS-DKV', 'nama_mapel' => 'Konsentrasi DKV'],
            ['kode_mapel' => 'MP-KONS-MP', 'nama_mapel' => 'Konsentrasi MP'],
            ['kode_mapel' => 'MP-KONS-PSPT', 'nama_mapel' => 'Konsentrasi PSPT'],
            ['kode_mapel' => 'MP-KONS-RPL', 'nama_mapel' => 'Konsentrasi RPL'],
            ['kode_mapel' => 'MP-KONS-TKI', 'nama_mapel' => 'Konsentrasi TKI'],
            ['kode_mapel' => 'MP-KONS-TKJ', 'nama_mapel' => 'Konsentrasi TKJ'],
            ['kode_mapel' => 'MP-KONS-ULW', 'nama_mapel' => 'Konsentrasi ULW'],

            ['kode_mapel' => 'MP-KWU', 'nama_mapel' => 'Kreativitas, Inovasi, dan Kewirausahaan'],

            ['kode_mapel' => 'MP-PILIH-AK', 'nama_mapel' => 'Mapel Pilihan AK'],
            ['kode_mapel' => 'MP-PILIH-AN', 'nama_mapel' => 'Mapel Pilihan AN'],
            ['kode_mapel' => 'MP-PILIH-BD', 'nama_mapel' => 'Mapel Pilihan BD'],
            ['kode_mapel' => 'MP-PILIH-DKV', 'nama_mapel' => 'Mapel Pilihan DKV'],
            ['kode_mapel' => 'MP-PILIH-MP', 'nama_mapel' => 'Mapel Pilihan MP'],
            ['kode_mapel' => 'MP-PILIH-PSPT', 'nama_mapel' => 'Mapel Pilihan PSPT'],
            ['kode_mapel' => 'MP-PILIH-RPL', 'nama_mapel' => 'Mapel Pilihan RPL'],
            ['kode_mapel' => 'MP-PILIH-TKI', 'nama_mapel' => 'Mapel Pilihan TKI'],
            ['kode_mapel' => 'MP-PILIH-TKJ', 'nama_mapel' => 'Mapel Pilihan TKJ'],

            ['kode_mapel' => 'MP-MAT', 'nama_mapel' => 'Matematika'],
            ['kode_mapel' => 'MP-PJOK', 'nama_mapel' => 'PJOK'],
            ['kode_mapel' => 'MP-PAI', 'nama_mapel' => 'Pendidikan Agama Islam dan Budi Pekerti'],
            ['kode_mapel' => 'MP-PANCASILA', 'nama_mapel' => 'Pendidikan Pancasila'],
            ['kode_mapel' => 'MP-SEJARAH', 'nama_mapel' => 'Sejarah'],
            ['kode_mapel' => 'MP-SENI', 'nama_mapel' => 'Seni Budaya'],
        ];

        foreach ($mapels as $mapel) {
            Mapel::updateOrCreate(
                ['kode_mapel' => $mapel['kode_mapel']],
                ['nama_mapel' => $mapel['nama_mapel']]
            );
        }
    }
}