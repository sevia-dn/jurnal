<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run() {

        // Akun Admin
        User::create([
            'name' => 'Admin Sekolah',
            'username' => 'admin1',
            'nip' => null,
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Akun Guru PNS (Login pakai NIP)
        User::create([
            'name' => 'Bada Maymunah S.Pd',
            'username' => 'badamayumunah',
            'nip' => '198501012010011001',
            'password' => Hash::make('bada123'),
            'role' => 'guru',
        ]);

        // Akun Guru Honorer (Login pakai Username)
        User::create([
            'name' => 'Anissa Ramadani S.Pd',
            'username' => 'anissaramadani',
            'nip' => null,
            'password' => Hash::make('anissa123'),
            'role' => 'guru',
        ]);

        // Akun Guru Piket
        User::create([
            'name' => 'Betti Sulisyowati S.Pd',
            'username' => 'bettisulisyowati',
            'nip' => '198501012010011002',
            'password' => Hash::make('betii123'),
            'role' => 'piket',
        ]);

        // Akun Waka Kesiswaan
        User::create([
            'name' => 'Fajar Siswanto S.Pd',
            'username' => 'fajarsiswanto',
            'nip' => '198501012010011003',
            'password' => Hash::make('fajar123'),
            'role' => 'waka',
        ]);

        // Akun Sekretaris Kelas
        User::create([
            'name' => 'xirekayasaperangkatlunak2',
            'username' => 'xirekayasaperangkatlunak2',
            'nip' => null,
            'password' => Hash::make('xirekayasa2'),
            'role' => 'pengurus_kelas',
        ]);
    }
}

