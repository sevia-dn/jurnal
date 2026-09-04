<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,          // Seeder untuk akun login (Admin, Guru, Piket, Sekretaris)
            KelasSeeder::class,         // Seeder untuk data kelas
            GuruSeeder::class,          // Seeder untuk data guru
            JadwalPelajaranSeeder::class, // Seeder untuk jadwal (butuh relasi ke kelas & guru)
            JurnalGuruSeeder::class,
        ]);
    }
}
