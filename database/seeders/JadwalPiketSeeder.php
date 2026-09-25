<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class JadwalPiketSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SeptemberPiketSeeder::class);
    }
}
