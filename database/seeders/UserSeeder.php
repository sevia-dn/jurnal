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
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Akun Guru Piket
        User::create([
            'name' => 'Petugas Guru Piket',
            'username' => 'piket',
            'nip' => '198501012010011002',
            'password' => Hash::make('password'),
            'role' => 'piket',
        ]);

        // 5 Akun Waka (Kurikulum, Kesiswaan, Sarpras, Humas, Mutu)
        User::create([
            'name' => 'Waka Kurikulum',
            'username' => 'waka_kurikulum',
            'nip' => '198501012010011004',
            'password' => Hash::make('password'),
            'role' => 'waka',
        ]);

        User::create([
            'name' => 'Waka Kesiswaan',
            'username' => 'waka',
            'nip' => '198501012010011003',
            'password' => Hash::make('password'),
            'role' => 'waka',
        ]);

        User::create([
            'name' => 'Waka Sarana & Prasarana',
            'username' => 'waka_sarpras',
            'nip' => '198501012010011005',
            'password' => Hash::make('password'),
            'role' => 'waka',
        ]);

        User::create([
            'name' => 'Waka Humas & Hubungan Industri',
            'username' => 'waka_humas',
            'nip' => '198501012010011006',
            'password' => Hash::make('password'),
            'role' => 'waka',
        ]);

        User::create([
            'name' => 'Waka Manajemen Mutu & SDM',
            'username' => 'waka_mutu',
            'nip' => '198501012010011007',
            'password' => Hash::make('password'),
            'role' => 'waka',
        ]);

        // Akun Sekretaris Kelas
        User::create([
            'name' => 'Sekretaris Kelas XI RPL',
            'username' => 'sekretaris',
            'nip' => null,
            'password' => Hash::make('password'),
            'role' => 'sekretaris',
        ]);

        // Akun Administrator
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'nip' => null,
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}