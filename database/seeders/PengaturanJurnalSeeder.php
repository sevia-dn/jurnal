<?php

namespace Database\Seeders;

use App\Models\PengaturanJurnal;
use App\Models\User;
use Illuminate\Database\Seeder;

class PengaturanJurnalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        PengaturanJurnal::updateOrCreate(
            ['id' => 1],
            [
                'kebijakan_tenggat' => 'jam_mengajar',
                'diubah_oleh' => $admin?->id,
            ]
        );
    }
}
