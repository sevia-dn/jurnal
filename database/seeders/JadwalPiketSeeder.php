<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalPiket;
use App\Models\User;

class JadwalPiketSeeder extends Seeder
{
    public function run(): void
    {
        JadwalPiket::query()->delete();

        $gurus = User::where('role', 'guru')->get();

        if ($gurus->count() === 0) {
            return;
        }

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        foreach ($days as $day) {
            // Shift 1: 07:00 - 11:00 (3 Guru per shift)
            foreach ($gurus->take(3) as $guru) {
                JadwalPiket::create([
                    'user_id' => $guru->id,
                    'hari' => $day,
                    'bulan' => date('n'),
                    'tahun' => date('Y'),
                    'shift' => 1,
                    'jam_mulai' => '07:00:00',
                    'jam_selesai' => '11:00:00',
                ]);
            }

            // Shift 2: 11:00 - 15:00 (3 Guru per shift)
            foreach ($gurus->skip(1)->take(3) as $guru) {
                JadwalPiket::create([
                    'user_id' => $guru->id,
                    'hari' => $day,
                    'bulan' => date('n'),
                    'tahun' => date('Y'),
                    'shift' => 2,
                    'jam_mulai' => '11:00:00',
                    'jam_selesai' => '15:00:00',
                ]);
            }
        }
    }
}
