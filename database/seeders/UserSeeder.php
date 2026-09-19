<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder {
    public function run() {

        // 1. Akun Admin Dev
        User::firstOrCreate(
            ['username' => 'admin1'],
            [
                'name' => 'Admin Sekolah',
                'nip' => null,
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 2. Akun Guru PNS (Login pakai NIP)
        User::firstOrCreate(
            ['username' => 'badamayumunah'],
            [
                'name' => 'Bada Maymunah S.Pd',
                'nip' => '198501012010011001',
                'password' => Hash::make('bada123'),
                'role' => 'guru',
            ]
        );

        // 3. Akun Guru Honorer (Login pakai Username)
        User::firstOrCreate(
            ['username' => 'anissaramadani'],
            [
                'name' => 'Anissa Ramadani S.Pd',
                'nip' => null,
                'password' => Hash::make('anissa123'),
                'role' => 'guru',
            ]
        );

        // 4. Akun Guru yang ditugaskan sebagai Piket
        User::firstOrCreate(
            ['username' => 'bettisulisyowati'],
            [
                'name' => 'Betti Sulisyowati S.Pd',
                'nip' => '198501012010011002',
                'password' => Hash::make('betii123'),
                'role' => 'guru',
            ]
        );

        // 5. Akun Guru yang ditugaskan sebagai Waka
        User::firstOrCreate(
            ['username' => 'fajarsiswanto'],
            [
                'name' => 'Fajar Siswanto S.Pd',
                'nip' => '198501012010011003',
                'password' => Hash::make('fajar123'),
                'role' => 'guru',
            ]
        );

        // 6. Akun Sekretaris Kelas
        User::firstOrCreate(
            ['username' => 'xirekayasaperangkatlunak2'],
            [
                'name' => 'xirekayasaperangkatlunak2',
                'nip' => null,
                'password' => Hash::make('xirekayasa2'),
                'role' => 'pengurus_kelas',
            ]
        );

        // 7. Master Guru dari master_data.json
        $jsonPath = base_path('scripts/master_data.json');
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
            $teachers = $data['teachers'] ?? [];

            $baseNip = 198501012010011000;
            foreach ($teachers as $idx => $tname) {
                $cleanName = strtolower(preg_replace('/[^a-zA-Z]/', '', $tname));
                
                // Cari apakah user guru dengan nama yang mirip sudah ada
                $existing = User::where('role', 'guru')
                    ->get()
                    ->first(function($u) use ($cleanName) {
                        return strtolower(preg_replace('/[^a-zA-Z]/', '', $u->name)) === $cleanName;
                    });

                if (!$existing) {
                    $rawSlug = Str::slug(explode(',', $tname)[0], '_');
                    if (empty($rawSlug)) $rawSlug = 'guru_' . ($idx + 1);
                    $username = $rawSlug;
                    $counter = 1;
                    while (User::where('username', $username)->exists()) {
                        $username = $rawSlug . '_' . $counter;
                        $counter++;
                    }

                    $nipNumber = (string) ($baseNip + $idx + 10);
                    while (User::where('nip', $nipNumber)->exists()) {
                        $baseNip += 100;
                        $nipNumber = (string) ($baseNip + $idx + 10);
                    }

                    $email = $username . '@smkn1boyolangu.sch.id';

                    User::create([
                        'name' => $tname,
                        'username' => $username,
                        'nip' => $nipNumber,
                        'email' => $email,
                        'role' => 'guru',
                        'password' => Hash::make('guru123'),
                    ]);
                }
            }
        }
    }
}

