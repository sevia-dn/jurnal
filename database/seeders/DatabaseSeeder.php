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
            UserSeeder::class,
            GuruSeeder::class,
            KelasSeeder::class,
            MapelSeeder::class,
            SiswaSeeder::class,
            JadwalPelajaranSeeder::class,
            JadwalMengajarSeeder::class,
            JurnalMengajarSeeder::class,
            JadwalPiketSeeder::class,    // Seeder jadwal piket shift 1 & 2
            AbsensiSeeder::class,
        ]);
    }
}
