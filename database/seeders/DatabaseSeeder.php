<?php

namespace Database\Seeders;

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
            UserSeeder::class,           // Mengisi akun multi-role (piket, waka, sekretaris, guru)
            KelasSeeder::class,          // Seeder kelas
            MapelSeeder::class,          
            SiswaSeeder::class,          
            JadwalPelajaranSeeder::class, 
            JurnalMengajarSeeder::class,
            JadwalPiketSeeder::class,    // Seeder jadwal piket shift 1 & 2
        ]);
    }
}
