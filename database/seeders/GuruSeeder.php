<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // 13 Guru spesifik beserta NIP
        $guruKhusus = [
            [
                'nama' => 'Ruly Dwi Setyaningrum, S.Kom.',
                'search' => 'Ruly Dwi Setyaningrum',
                'nip' => '19850418 201001 2 031',
            ],
            [
                'nama' => 'Badrus Sulaiman, S. Pd, Gr.',
                'search' => 'Badrus Sulaiman',
                'nip' => '19900418 202012 1 017',
            ],
            [
                'nama' => 'Kurnila Putri Islamawati, S.Pd.',
                'search' => 'Kurnila Putri Islamawati',
                'nip' => '19970318 202221 2 010',
            ],
            [
                'nama' => 'Elyana Frisca Monica, S. Pd.',
                'search' => 'Elyana Frisca Monica',
                'nip' => '19920504 202221 2 022',
            ],
            [
                'nama' => 'Winartin, S.Pd.',
                'search' => 'Winartin',
                'nip' => '19801224 200801 2 016',
            ],
            [
                'nama' => 'Lutfia Marsalina, S.Pd.I, M.Pd.',
                'search' => 'Lutfia Marsalina',
                'nip' => '19800329 200901 2 006',
            ],
            [
                'nama' => 'Fajar Wahyu Pratiwi, S.S.',
                'search' => 'Fajar Wahyu Pratiwi',
                'nip' => '19820529 202321 2 015',
            ],
            [
                'nama' => 'Fitri Amaliyah, S.Pd.',
                'search' => 'Fitri Amaliyah',
                'nip' => '19800312 202421 2 013',
            ],
            [
                'nama' => 'Yustin Febrini, S.Pd.',
                'search' => 'Yustin Febrini',
                'nip' => '19920205 202521 2 129',
            ],
            [
                'nama' => 'Dra. Hanik Pangestuti',
                'search' => 'Hanik Pangestuti',
                'nip' => '19670512 202221 2 003',
            ],
            [
                'nama' => 'Laili Ermawati, S.Pd.',
                'search' => 'Laili Ermawati',
                'nip' => '19850418 201001 2 032', // Disesuaikan digit akhir agar tidak duplikat dengan Bu Ruly
            ],
            [
                'nama' => 'Wiwik Yuniarsih, S.H., S.Pd., M.H.',
                'search' => 'Wiwik Yuniarsih',
                'nip' => '19750616 202321 2 007',
            ],
            [
                'nama' => 'Angga Widhy Wirawan, S.Pd., M.Pd.',
                'search' => 'Angga Widhy Wirawan',
                'nip' => '19860127 201101 1 013',
            ],
        ];

        foreach ($guruKhusus as $data) {
            $username = Str::slug($data['nama'], '');
            $user = User::where('name', 'like', '%'.$data['search'].'%')->first();

            if ($user) {
                $user->update([
                    'name' => $data['nama'],
                    'nip' => $data['nip'],
                    'role' => 'guru',
                ]);
            } else {
                User::create([
                    'name' => $data['nama'],
                    'username' => $username,
                    'nip' => $data['nip'],
                    'email' => $username.'@jurnalkita.local',
                    'password' => Hash::make('guru123'),
                    'role' => 'guru',
                ]);
            }
        }

        // Daftar guru umum lainnya
        $guruLainnya = [
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
            'Widodo, S.Pd',
            'Khoyrotun Hisani, S.Sn',
            'Astra Bella Flamboyan, S.Psi',
            'Danang Anjar Hymawanto, S.Pd',
            'Istiana Suhartati, S.T',
            'Elysa Yuli Nur\'aini, S.Si',
            'Indriati, S.Pd',
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
            'Dyah Esti Rahayu, S.Pd',
            'Yuli Ratnasari, S.Pd',
            'Pipit Ambarwati, S.Pd',
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

        foreach ($guruLainnya as $nama) {
            $username = Str::slug($nama, '');
            $user = User::where('name', $nama)->first();

            if (! $user) {
                User::create([
                    'name' => $nama,
                    'username' => $username,
                    'nip' => null,
                    'email' => $username.'@jurnalkita.local',
                    'password' => Hash::make('guru123'),
                    'role' => 'guru',
                ]);
            } else {
                $user->update([
                    'role' => 'guru',
                ]);
            }
        }
    }
}
