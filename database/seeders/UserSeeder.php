<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Akun Admin
        User::updateOrCreate(
            ['username' => 'admin1'],
            [
                'name' => 'Admin Sekolah',
                'nip' => null,
                'email' => 'admin1@jurnalkita.local',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 2. Akun Guru Piket
        User::updateOrCreate(
            ['username' => 'bettisulisyowati'],
            [
                'name' => 'Betti Sulisyowati S.Pd',
                'nip' => '198501012010011002',
                'email' => 'bettisulisyowati@jurnalkita.local',
                'password' => Hash::make('betii123'),
                'role' => 'piket',
            ]
        );

        // 3. Akun Waka Kesiswaan
        User::updateOrCreate(
            ['username' => 'fajarsiswanto'],
            [
                'name' => 'Fajar Siswanto S.Pd',
                'nip' => '198501012010011003',
                'email' => 'fajarsiswanto@jurnalkita.local',
                'password' => Hash::make('fajar123'),
                'role' => 'waka',
            ]
        );

        // 4. Akun Pengurus / Sekretaris Kelas
        User::updateOrCreate(
            ['username' => 'xirekayasaperangkatlunak2'],
            [
                'name' => 'XI Rekayasa Perangkat Lunak 2',
                'nip' => null,
                'email' => 'xirekayasaperangkatlunak2@jurnalkita.local',
                'password' => Hash::make('xirekayasa2'),
                'role' => 'pengurus_kelas',
            ]
        );

        // 5. 13 Akun Guru Pengajar (Login via NIP atau Username)
        $daftarGuru = [
            [
                'name' => 'Ruly Dwi Setyaningrum, S.Kom.',
                'username' => 'rulydwisetyaningrum',
                'search' => 'Ruly Dwi Setyaningrum',
                'nip' => '19850418 201001 2 031',
                'password' => 'ruly123',
            ],
            [
                'name' => 'Badrus Sulaiman, S. Pd, Gr.',
                'username' => 'badrussulaiman',
                'search' => 'Badrus Sulaiman',
                'nip' => '19900418 202012 1 017',
                'password' => 'badrus123',
            ],
            [
                'name' => 'Kurnila Putri Islamawati, S.Pd.',
                'username' => 'kurnilaputri',
                'search' => 'Kurnila Putri Islamawati',
                'nip' => '19970318 202221 2 010',
                'password' => 'kurnila123',
            ],
            [
                'name' => 'Elyana Frisca Monica, S. Pd.',
                'username' => 'elyanafrisca',
                'search' => 'Elyana Frisca Monica',
                'nip' => '19920504 202221 2 022',
                'password' => 'elyana123',
            ],
            [
                'name' => 'Winartin, S.Pd.',
                'username' => 'winartin',
                'search' => 'Winartin',
                'nip' => '19801224 200801 2 016',
                'password' => 'winartin123',
            ],
            [
                'name' => 'Lutfia Marsalina, S.Pd.I, M.Pd.',
                'username' => 'lutfiamarsalina',
                'search' => 'Lutfia Marsalina',
                'nip' => '19800329 200901 2 006',
                'password' => 'lutfia123',
            ],
            [
                'name' => 'Fajar Wahyu Pratiwi, S.S.',
                'username' => 'fajarwahyu',
                'search' => 'Fajar Wahyu Pratiwi',
                'nip' => '19820529 202321 2 015',
                'password' => 'fajar123',
            ],
            [
                'name' => 'Fitri Amaliyah, S.Pd.',
                'username' => 'fitriamaliyah',
                'search' => 'Fitri Amaliyah',
                'nip' => '19800312 202421 2 013',
                'password' => 'fitri123',
            ],
            [
                'name' => 'Yustin Febrini, S.Pd.',
                'username' => 'yustinfebrini',
                'search' => 'Yustin Febrini',
                'nip' => '19920205 202521 2 129',
                'password' => 'yustin123',
            ],
            [
                'name' => 'Dra. Hanik Pangestuti',
                'username' => 'hanikpangestuti',
                'search' => 'Hanik Pangestuti',
                'nip' => '19670512 202221 2 003',
                'password' => 'hanik123',
            ],
            [
                'name' => 'Laili Ermawati, S.Pd.',
                'username' => 'lailiermawati',
                'search' => 'Laili Ermawati',
                'nip' => '19850418 201001 2 032',
                'password' => 'laili123',
            ],
            [
                'name' => 'Wiwik Yuniarsih, S.H., S.Pd., M.H.',
                'username' => 'wiwikyuniarsih',
                'search' => 'Wiwik Yuniarsih',
                'nip' => '19750616 202321 2 007',
                'password' => 'wiwik123',
            ],
            [
                'name' => 'Angga Widhy Wirawan, S.Pd., M.Pd.',
                'username' => 'anggawidhy',
                'search' => 'Angga Widhy Wirawan',
                'nip' => '19860127 201101 1 013',
                'password' => 'angga123',
            ],
        ];

        foreach ($daftarGuru as $guru) {
            // Cari guru yang sudah ada berdasarkan nama untuk menjaga relasi jadwal_mengajar
            $user = User::where('name', 'like', '%'.$guru['search'].'%')->first();

            if ($user) {
                $user->update([
                    'name' => $guru['name'],
                    'username' => $guru['username'],
                    'nip' => $guru['nip'],
                    'password' => Hash::make($guru['password']),
                    'role' => 'guru',
                ]);
            } else {
                User::create([
                    'name' => $guru['name'],
                    'username' => $guru['username'],
                    'nip' => $guru['nip'],
                    'email' => $guru['username'].'@jurnalkita.local',
                    'password' => Hash::make($guru['password']),
                    'role' => 'guru',
                ]);
            }
        }
    }
}
