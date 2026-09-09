<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run() {

        // Akun Guru PNS (Login pakai NIP)
        User::create([
            'name' => 'Bapak Guru PNS',
            'username' => 'guru_pns',
            'nip' => '198501012010011001',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Akun Guru Honorer (Login pakai Username)
        User::create([
            'name' => 'Ibu Guru Honorer',
            'username' => 'guru_honorer',
            'nip' => null,
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        // Akun Guru Piket
        User::create([
            'name' => 'Petugas Guru Piket',
            'username' => 'piket',
            'nip' => '198501012010011002',
            'password' => Hash::make('password123'),
            'role' => 'piket',
        ]);

        // Akun Waka Kesiswaan
        User::create([
            'name' => 'Waka Kesiswaan',
            'username' => 'waka',
            'nip' => '198501012010011003',
            'password' => Hash::make('password'),
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

