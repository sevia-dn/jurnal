<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::updateOrCreate(
            ['username' => 'admin1'],
            [
                'name' => 'Admin Sekolah',
                'nip' => null,
                'email' => null,
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
                'email' => null,
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
                'email' => null,
                'password' => Hash::make('fajar123'),
                'role' => 'waka',
            ]
        );

        // 4. 48 Akun Pengurus / Sekretaris Kelas (Sesuai Jadwal KBM SMKN 1 Boyolangu)
        $daftarKelas = [
            // Kelas X (24 Kelas)
            ['name' => 'Pengurus Kelas X TKI 1', 'username' => 'xtki1', 'password' => 'xtki1'],
            ['name' => 'Pengurus Kelas X TKI 2', 'username' => 'xtki2', 'password' => 'xtki2'],
            ['name' => 'Pengurus Kelas X RPL 1', 'username' => 'xrpl1', 'password' => 'xrpl1'],
            ['name' => 'Pengurus Kelas X RPL 2', 'username' => 'xrpl2', 'password' => 'xrpl2'],
            ['name' => 'Pengurus Kelas X TKJ 1', 'username' => 'xtkj1', 'password' => 'xtkj1'],
            ['name' => 'Pengurus Kelas X TKJ 2', 'username' => 'xtkj2', 'password' => 'xtkj2'],
            ['name' => 'Pengurus Kelas X BD 1', 'username' => 'xbd1', 'password' => 'xbd1'],
            ['name' => 'Pengurus Kelas X BD 2', 'username' => 'xbd2', 'password' => 'xbd2'],
            ['name' => 'Pengurus Kelas X BD 3', 'username' => 'xbd3', 'password' => 'xbd3'],
            ['name' => 'Pengurus Kelas X MP 1', 'username' => 'xmp1', 'password' => 'xmp1'],
            ['name' => 'Pengurus Kelas X MP 2', 'username' => 'xmp2', 'password' => 'xmp2'],
            ['name' => 'Pengurus Kelas X MP 3', 'username' => 'xmp3', 'password' => 'xmp3'],
            ['name' => 'Pengurus Kelas X MP 4', 'username' => 'xmp4', 'password' => 'xmp4'],
            ['name' => 'Pengurus Kelas X AK 1', 'username' => 'xak1', 'password' => 'xak1'],
            ['name' => 'Pengurus Kelas X AK 2', 'username' => 'xak2', 'password' => 'xak2'],
            ['name' => 'Pengurus Kelas X AK 3', 'username' => 'xak3', 'password' => 'xak3'],
            ['name' => 'Pengurus Kelas X AK 4', 'username' => 'xak4', 'password' => 'xak4'],
            ['name' => 'Pengurus Kelas X ULW', 'username' => 'xulw', 'password' => 'xulw'],
            ['name' => 'Pengurus Kelas X DKV 1', 'username' => 'xdkv1', 'password' => 'xdkv1'],
            ['name' => 'Pengurus Kelas X DKV 2', 'username' => 'xdkv2', 'password' => 'xdkv2'],
            ['name' => 'Pengurus Kelas X PSPT 1', 'username' => 'xpspt1', 'password' => 'xpspt1'],
            ['name' => 'Pengurus Kelas X PSPT 2', 'username' => 'xpspt2', 'password' => 'xpspt2'],
            ['name' => 'Pengurus Kelas X AN 1', 'username' => 'xan1', 'password' => 'xan1'],
            ['name' => 'Pengurus Kelas X AN 2', 'username' => 'xan2', 'password' => 'xan2'],

            // Kelas XI (24 Kelas)
            ['name' => 'Pengurus Kelas XI TKI 1', 'username' => 'xitki1', 'password' => 'xitki1'],
            ['name' => 'Pengurus Kelas XI TKI 2', 'username' => 'xitki2', 'password' => 'xitki2'],
            ['name' => 'Pengurus Kelas XI RPL 1', 'username' => 'xirpl1', 'password' => 'xirpl1'],
            ['name' => 'Pengurus Kelas XI RPL 2', 'username' => 'xirpl2', 'password' => 'xirpl2'],
            ['name' => 'Pengurus Kelas XI TKJ 1', 'username' => 'xitkj1', 'password' => 'xitkj1'],
            ['name' => 'Pengurus Kelas XI TKJ 2', 'username' => 'xitkj2', 'password' => 'xitkj2'],
            ['name' => 'Pengurus Kelas XI BD 1', 'username' => 'xibd1', 'password' => 'xibd1'],
            ['name' => 'Pengurus Kelas XI BD 2', 'username' => 'xibd2', 'password' => 'xibd2'],
            ['name' => 'Pengurus Kelas XI BD 3', 'username' => 'xibd3', 'password' => 'xibd3'],
            ['name' => 'Pengurus Kelas XI MP 1', 'username' => 'ximp1', 'password' => 'ximp1'],
            ['name' => 'Pengurus Kelas XI MP 2', 'username' => 'ximp2', 'password' => 'ximp2'],
            ['name' => 'Pengurus Kelas XI MP 3', 'username' => 'ximp3', 'password' => 'ximp3'],
            ['name' => 'Pengurus Kelas XI MP 4', 'username' => 'ximp4', 'password' => 'ximp4'],
            ['name' => 'Pengurus Kelas XI AK 1', 'username' => 'xiak1', 'password' => 'xiak1'],
            ['name' => 'Pengurus Kelas XI AK 2', 'username' => 'xiak2', 'password' => 'xiak2'],
            ['name' => 'Pengurus Kelas XI AK 3', 'username' => 'xiak3', 'password' => 'xiak3'],
            ['name' => 'Pengurus Kelas XI AK 4', 'username' => 'xiak4', 'password' => 'xiak4'],
            ['name' => 'Pengurus Kelas XI ULW', 'username' => 'xiulw', 'password' => 'xiulw'],
            ['name' => 'Pengurus Kelas XI DKV 1', 'username' => 'xidkv1', 'password' => 'xidkv1'],
            ['name' => 'Pengurus Kelas XI DKV 2', 'username' => 'xidkv2', 'password' => 'xidkv2'],
            ['name' => 'Pengurus Kelas XI PSPT 1', 'username' => 'xipspt1', 'password' => 'xipspt1'],
            ['name' => 'Pengurus Kelas XI PSPT 2', 'username' => 'xipspt2', 'password' => 'xipspt2'],
            ['name' => 'Pengurus Kelas XI AN 1', 'username' => 'xian1', 'password' => 'xian1'],
            ['name' => 'Pengurus Kelas XI AN 2', 'username' => 'xian2', 'password' => 'xian2'],
        ];

        foreach ($daftarKelas as $kelas) {
            User::updateOrCreate(
                ['username' => $kelas['username']],
                [
                    'name' => $kelas['name'],
                    'nip' => null,
                    'email' => null,
                    'password' => Hash::make($kelas['password']),
                    'role' => 'pengurus_kelas',
                ]
            );
        }

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
            $user = User::where('name', 'like', '%'.$guru['search'].'%')->first();

            if ($user) {
                $user->update([
                    'name' => $guru['name'],
                    'username' => $guru['username'],
                    'nip' => $guru['nip'],
                    'email' => null,
                    'password' => Hash::make($guru['password']),
                    'role' => 'guru',
                ]);
            } else {
                User::create([
                    'name' => $guru['name'],
                    'username' => $guru['username'],
                    'nip' => $guru['nip'],
                    'email' => null,
                    'password' => Hash::make($guru['password']),
                    'role' => 'guru',
                ]);
            }
        }
    }
}
