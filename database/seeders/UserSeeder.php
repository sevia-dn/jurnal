<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run() {
        // Hapus data lama jika ada
        User::query()->delete();

        // Akun Admin
        User::create([
            'name' => 'Admin Sekolah',
            'username' => 'admin1',
            'nip' => null,
            'no_hp' => '081100001111',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_waka' => false
        ]);

        // Akun Guru PNS (Login pakai NIP / Username)
        $guru1 = User::create([
            'name' => 'Bada Maymunah, S.Pd',
            'username' => 'badamaymunah',
            'nip' => '198501012010011001',
            'no_hp' => '081234567891',
            'password' => Hash::make('bada123'),
            'role' => 'guru',
            'is_waka' => false
        ]);

        // Akun Guru Honorer
        $guru2 = User::create([
            'name' => 'Anissa Ramadani, S.Pd',
            'username' => 'anissaramadani',
            'nip' => '199002022015022002',
            'no_hp' => '081234567892',
            'password' => Hash::make('anissa123'),
            'role' => 'guru',
            'is_waka' => false
        ]);

        // Akun Guru yang Terjadwal Piket
        $guruPiket = User::create([
            'name' => 'Betti Sulisyowati, S.Pd',
            'username' => 'bettisulisyowati',
            'nip' => '198501012010011002',
            'no_hp' => '081234567893',
            'password' => Hash::make('betti123'),
            'role' => 'guru',
            'is_waka' => false
        ]);

        // Akun Guru Merangkap Waka Kesiswaan
        $waka = User::create([
            'name' => 'Fajar Siswanto, S.Pd (Waka Kesiswaan)',
            'username' => 'fajarsiswanto',
            'nip' => '198501012010011003',
            'no_hp' => '081299998888',
            'password' => Hash::make('fajar123'),
            'role' => 'guru',
            'is_waka' => true
        ]);

        // Akun Pengurus Kelas / Sekretaris
        User::create([
            'name' => 'Pengurus Kelas XI RPL 2',
            'username' => 'xirekayasaperangkatlunak2',
            'nip' => null,
            'no_hp' => '081234567894',
            'password' => Hash::make('xirekayasa2'),
            'role' => 'pengurus_kelas',
            'is_waka' => false
        ]);
    }
}
