<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $guru = [
            'Bada Maymunah S.Pd',
            'Anissa Ramadani S.Pd',

            'Rulik Indrawati, S.Pd',
            'Mega Mahardika, S.Pd',
            'Veronica Damay Rulitasari, S.Pd',
            'Dra. Anik Indriani',
            "Muto'atul Khosi'ah, S.Pd",
            'Rizki Putri Wulandari, S.Pd',
            'Zainul Arifin, S.Pd',
            'Alfinu Farikh Abdillah, S.Pd.I',
            'Sulistyowati, SS',
            'Tutut Sriatin, S.Pd',
            'Endang Safitri, S.Pd',
            'Abdul Rohman, S.Pd',

            'Basuki Sarjono, S.Pd',
            'Komariyah, S.Pd',
            'Ilham Sungeidi, S.Pd',
            'Sri Kusumastuti, S.Pd',
            'Baskoro, S.Si',
            "Rifkotin Na'imah, S.Pd",
            'Yani, S.Pd.',
            'Diana Hartanti, S.T., M.Pd',
            'Ayu Puspitorini, ST',
            'Mufatiroh, S.Ag',
            'Yustin Febrini, S.Pd',
            'Kurnila Putri Islamawati, S.Pd',
            'Widodo, S.Pd',

            'Khoyrotun Hisani, S.Sn',
            'Astra Bella Flamboyan, S.Psi',
            'Danang Anjar Hymawanto, S.Pd',
            'Istiana Suhartati, S.T',
            'Elysa Yuli Nur\'aini, S.Si',
            'Indriati, S.Pd',
            'Fajar Wahyu Pratiwi, S.S',
            'Sinta Lestari, S.Pd.I',
            "Mas'an Widodo, S.Pd., M.T.",
            'Endik Kuswantoro, S.Kom., M.T',
            'Agus Pramono, S.Sn',

            'Umi Kulsum, S.Pd',
            'Benny Mamora, S.Kom',
            'Joko Priyanto, S.Kom',
            'Tuhu Eries Kudori, S.Sn',
            "Sa'ad Wazis Hiedayat, S.Pd",
            'Bella Prakoso, S.Pd',

            'Wiwik Yuniarsih, S.Pd',
            'Dyah Esti Rahayu, S.Pd',
            'Yuli Ratnasari, S.Pd',
            'Pipit Ambarwati, S.Pd',
            'Winartin, S.Pd',
            'Setiyo Winarko, S.Pd',
            'Kasmi, S.Pd., M.Pd',
            'Ajeng Okvitasari, S.Pd',
            'Winarsih, S.Pd, M.Pd',
            'Atih Wilupi, S.E., M.Pd',
            'Indayah, S.Pd., M.Pd',
            'Arvia Rienetasary, S.Pd',
            'Nur Eko Wahyuningsih, S.Pd',

            'Isti Mufadah, S.Pd',
            'Luluk Munfarida, S.Pd',
            'Dra. Hanik Pangestuti',
            'Endang Ary Handayani, S.T., M.Pd',
            'Erna Qoriah, S.E.',
            'Ary Sunaryo, ST., M.Pd',
            'Siswanti Purwaningsih, S.T., M.Pd',
            'Siti Munawaroh, S.Kom., M.Pd',
            'Andri Krisdianto, SE., M.Pd',

            'Siti Umiharsih, S.Pd',
            'Arif Setyobudi, S.Pd',
            'Andika Christian Sasmita, S.ST',
            'Dhuana Putri Puspitasary, S.Pd',
            'Erwan Septiyono, S.Pd',
            'Rika Okta Maulida, S.Ds.',
        ];

        foreach ($guru as $nama) {
            // Buat username dari nama
            $username = Str::slug($nama, '');

            // Cari berdasarkan nama supaya tidak membuat duplikat
            $user = User::where('name', $nama)->first();

            if (!$user) {
                User::create([
                    'name' => $nama,
                    'username' => $username,
                    'nip' => null,
                    'email' => $username . '@jurnalkita.local',
                    'password' => Hash::make('guru123'),
                    'role' => 'guru',
                ]);
            } else {
                // Kalau sudah ada, pastikan role-nya guru
                $user->update([
                    'role' => 'guru',
                ]);
            }
        }
    }
}