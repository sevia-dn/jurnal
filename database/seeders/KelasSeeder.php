<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = [
            // Kelas X (24 Kelas)
            ['id_kelas' => 1, 'nama_kelas' => 'X TKI 1', 'wali_kelas' => 'Muashofah, M.Pd'],
            ['id_kelas' => 2, 'nama_kelas' => 'X TKI 2', 'wali_kelas' => 'Sri Kusumastuti, S.Pd'],
            ['id_kelas' => 3, 'nama_kelas' => 'X RPL 1', 'wali_kelas' => 'Indriati, S.Pd'],
            ['id_kelas' => 4, 'nama_kelas' => 'X RPL 2', 'wali_kelas' => 'Umi Kulsum, S.Pd'],
            ['id_kelas' => 5, 'nama_kelas' => 'X TKJ 1', 'wali_kelas' => 'Listyana Hartati, S.Kom., M.Pd'],
            ['id_kelas' => 6, 'nama_kelas' => 'X TKJ 2', 'wali_kelas' => 'Muhammad Fajar Assidiqi, S.Pd'],
            ['id_kelas' => 7, 'nama_kelas' => 'X BD 1', 'wali_kelas' => 'Ratih Dian Irawati, S.E'],
            ['id_kelas' => 8, 'nama_kelas' => 'X BD 2', 'wali_kelas' => 'Erna Qoriah, S.Pd'],
            ['id_kelas' => 9, 'nama_kelas' => 'X BD 3', 'wali_kelas' => 'Retno Widiyastuti, S.Pd'],
            ['id_kelas' => 10, 'nama_kelas' => 'X MP 1', 'wali_kelas' => 'Tutut Sriatin, S.Pd'],
            ['id_kelas' => 11, 'nama_kelas' => 'X MP 2', 'wali_kelas' => 'Peni Wulandari, S.Pd'],
            ['id_kelas' => 12, 'nama_kelas' => 'X MP 3', 'wali_kelas' => 'Yustin Febrini, S.Pd'],
            ['id_kelas' => 13, 'nama_kelas' => 'X MP 4', 'wali_kelas' => 'Rindang Rejeki, S.Pd'],
            ['id_kelas' => 14, 'nama_kelas' => 'X AK 1', 'wali_kelas' => 'Atih Wilupi, SE, M.Pd'],
            ['id_kelas' => 15, 'nama_kelas' => 'X AK 2', 'wali_kelas' => 'Yuli Ratnasari, S.Pd'],
            ['id_kelas' => 16, 'nama_kelas' => 'X AK 3', 'wali_kelas' => 'Ista Nofasari, S.Pd'],
            ['id_kelas' => 17, 'nama_kelas' => 'X AK 4', 'wali_kelas' => 'Astra Bela Flamboyan, S.Psi'],
            ['id_kelas' => 18, 'nama_kelas' => 'X ULW', 'wali_kelas' => 'Dwi Nova Setyandari, S.Pd'],
            ['id_kelas' => 19, 'nama_kelas' => 'X DKV 1', 'wali_kelas' => 'Rulik Indrawati, S.Pd'],
            ['id_kelas' => 20, 'nama_kelas' => 'X DKV 2', 'wali_kelas' => 'Khoyrotun Hisani, S.Sn'],
            ['id_kelas' => 21, 'nama_kelas' => 'X PSPT 1', 'wali_kelas' => 'Benny Mamora, S.Kom'],
            ['id_kelas' => 22, 'nama_kelas' => 'X PSPT 2', 'wali_kelas' => "Muto'atul Khosi'ah, S.Pd"],
            ['id_kelas' => 23, 'nama_kelas' => 'X AN 1', 'wali_kelas' => 'Dhuana Putri Puspitasary, S.Pd'],
            ['id_kelas' => 24, 'nama_kelas' => 'X AN 2', 'wali_kelas' => 'Rika Okta Maulida, S.Ds'],

            // Kelas XI (24 Kelas)
            ['id_kelas' => 25, 'nama_kelas' => 'XI TKI 1', 'wali_kelas' => 'Diana Hartanti, S.T'],
            ['id_kelas' => 26, 'nama_kelas' => 'XI TKI 2', 'wali_kelas' => 'Yuni Jiastuti, S.Pd'],
            ['id_kelas' => 27, 'nama_kelas' => 'XI RPL 1', 'wali_kelas' => 'Sulistyowati, S.S'],
            ['id_kelas' => 28, 'nama_kelas' => 'XI RPL 2', 'wali_kelas' => 'Winartin, S.Pd'],
            ['id_kelas' => 29, 'nama_kelas' => 'XI TKJ 1', 'wali_kelas' => 'Sri Rahayu, S.Pd'],
            ['id_kelas' => 30, 'nama_kelas' => 'XI TKJ 2', 'wali_kelas' => 'Fitri Amaliyah, S.Pd'],
            ['id_kelas' => 31, 'nama_kelas' => 'XI BD 1', 'wali_kelas' => 'Nur Eko Wahyuningsih, S.Pd'],
            ['id_kelas' => 32, 'nama_kelas' => 'XI BD 2', 'wali_kelas' => 'Erna Rinawati, S.Pd'],
            ['id_kelas' => 33, 'nama_kelas' => 'XI BD 3', 'wali_kelas' => 'Luluk Munfarida, S.Pd'],
            ['id_kelas' => 34, 'nama_kelas' => 'XI MP 1', 'wali_kelas' => 'Martiin, S.Pd'],
            ['id_kelas' => 35, 'nama_kelas' => 'XI MP 2', 'wali_kelas' => 'Sunarti, S.Pd'],
            ['id_kelas' => 36, 'nama_kelas' => 'XI MP 3', 'wali_kelas' => 'Titik Samsistini, S.Pd'],
            ['id_kelas' => 37, 'nama_kelas' => 'XI MP 4', 'wali_kelas' => 'Mega Mahardika, S.Pd'],
            ['id_kelas' => 38, 'nama_kelas' => 'XI AK 1', 'wali_kelas' => 'Dra. Anik Indriani'],
            ['id_kelas' => 39, 'nama_kelas' => 'XI AK 2', 'wali_kelas' => 'Pipit Ambarwati, S.Pd'],
            ['id_kelas' => 40, 'nama_kelas' => 'XI AK 3', 'wali_kelas' => 'Arvia Rienitasary, S.Pd'],
            ['id_kelas' => 41, 'nama_kelas' => 'XI AK 4', 'wali_kelas' => 'Ninik Sriwidayati, S.Pd'],
            ['id_kelas' => 42, 'nama_kelas' => 'XI ULW', 'wali_kelas' => 'Risqi Nur Imama, S.ST,Par'],
            ['id_kelas' => 43, 'nama_kelas' => 'XI DKV 1', 'wali_kelas' => 'Sinta Lestari, S.Pd.I'],
            ['id_kelas' => 44, 'nama_kelas' => 'XI DKV 2', 'wali_kelas' => 'Endik Kuswantoro, S.Kom'],
            ['id_kelas' => 45, 'nama_kelas' => 'XI PSPT 1', 'wali_kelas' => 'Winarsih, S.Pd, M.Pd'],
            ['id_kelas' => 46, 'nama_kelas' => 'XI PSPT 2', 'wali_kelas' => 'Tuhu Eries Kudori, S.Sn'],
            ['id_kelas' => 47, 'nama_kelas' => 'XI AN 1', 'wali_kelas' => 'Khuriyatul Kamila, S.Pd'],
            ['id_kelas' => 48, 'nama_kelas' => 'XI AN 2', 'wali_kelas' => 'Arif Setyobudi, S.Pd'],

            // Kelas XII (24 Kelas)
            ['id_kelas' => 49, 'nama_kelas' => 'XII TKI 1', 'wali_kelas' => "Rifkotin Na'imah, S.Pd"],
            ['id_kelas' => 50, 'nama_kelas' => 'XII TKI 2', 'wali_kelas' => 'Basuki Sarjono, S.Pd'],
            ['id_kelas' => 51, 'nama_kelas' => 'XII RPL 1', 'wali_kelas' => 'Andri Retno Yuli Astuti, S.Pd'],
            ['id_kelas' => 52, 'nama_kelas' => 'XII RPL 2', 'wali_kelas' => 'Badrus Sulaiman, S.Pd'],
            ['id_kelas' => 53, 'nama_kelas' => 'XII TKJ 1', 'wali_kelas' => 'Siswanti Purwaningsih, ST'],
            ['id_kelas' => 54, 'nama_kelas' => 'XII TKJ 2', 'wali_kelas' => 'Nishfu Laili, S.Pd'],
            ['id_kelas' => 55, 'nama_kelas' => 'XII BD 1', 'wali_kelas' => 'Nurul Azizah, S.Pd'],
            ['id_kelas' => 56, 'nama_kelas' => 'XII BD 2', 'wali_kelas' => 'Anisa Kusumawati, S.Pd'],
            ['id_kelas' => 57, 'nama_kelas' => 'XII BD 3', 'wali_kelas' => 'Niken Dewi Hastika, S.Pd'],
            ['id_kelas' => 58, 'nama_kelas' => 'XII MP 1', 'wali_kelas' => 'Ajeng Okvitasari, S.Pd'],
            ['id_kelas' => 59, 'nama_kelas' => 'XII MP 2', 'wali_kelas' => 'Abdul Rohman, S.Pd'],
            ['id_kelas' => 60, 'nama_kelas' => 'XII MP 3', 'wali_kelas' => 'Fitria Dyah Ayu Hartati, S.Pd'],
            ['id_kelas' => 61, 'nama_kelas' => 'XII MP 4', 'wali_kelas' => 'Veronica Damay Rulitasari, S.Pd'],
            ['id_kelas' => 62, 'nama_kelas' => 'XII AK 1', 'wali_kelas' => 'Indayah, S.Pd'],
            ['id_kelas' => 63, 'nama_kelas' => 'XII AK 2', 'wali_kelas' => 'Siti Umiharsih, S.Pd'],
            ['id_kelas' => 64, 'nama_kelas' => 'XII AK 3', 'wali_kelas' => 'Kasmi, S.Pd'],
            ['id_kelas' => 65, 'nama_kelas' => 'XII AK 4', 'wali_kelas' => 'Septiani, S.Pd., M.Pd'],
            ['id_kelas' => 66, 'nama_kelas' => 'XII ULW', 'wali_kelas' => 'Fitria Renytasari, S.Pd'],
            ['id_kelas' => 67, 'nama_kelas' => 'XII DKV 1', 'wali_kelas' => 'Fajar Wahyu Pratiwi, S.S'],
            ['id_kelas' => 68, 'nama_kelas' => 'XII DKV 2', 'wali_kelas' => 'Elysa Yuli Nuraini, S.SI'],
            ['id_kelas' => 69, 'nama_kelas' => 'XII PSPT 1', 'wali_kelas' => 'Wiwik Yuniarsih, S.Pd'],
            ['id_kelas' => 70, 'nama_kelas' => 'XII PSPT 2', 'wali_kelas' => 'Mufatiroh, S.Ag'],
            ['id_kelas' => 71, 'nama_kelas' => 'XII AN 1', 'wali_kelas' => 'Siti Maisaroh, S.Pd'],
            ['id_kelas' => 72, 'nama_kelas' => 'XII AN 2', 'wali_kelas' => 'Siti Khoiriyah, S.Pd'],
        ];

        foreach ($kelas as $item) {
            Kelas::updateOrCreate(
                ['id_kelas' => $item['id_kelas']],
                [
                    'nama_kelas' => $item['nama_kelas'],
                    'wali_kelas' => $item['wali_kelas'],
                    'jumlah_siswa' => 0,
                ]
            );
        }
    }
}
