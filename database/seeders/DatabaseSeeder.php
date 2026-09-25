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
            GuruSeeder::class,
            UserSeeder::class,
            KelasSeeder::class,
            MapelSeeder::class,
            SiswaSeeder::class,
            JadwalPelajaranSeeder::class,
            JadwalMengajarSeeder::class,
            JurnalMengajarSeeder::class,
            JadwalPiketSeeder::class,
            AbsensiSeeder::class,
        ]);
    }
}
