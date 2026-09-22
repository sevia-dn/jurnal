<?php

namespace Database\Seeders;

use App\Models\JadwalPiket;
use App\Models\User;
use Illuminate\Database\Seeder;

class JadwalPiketSeeder extends Seeder
{
    public function run(): void
    {
        JadwalPiket::query()->delete();

        $gurus = User::where('role', 'guru')->get();

        if ($gurus->count() === 0) {
            return;
        }

        $guruBada = $gurus->firstWhere('username', 'badamaymunah') ?? $gurus->first();
        $guruAnissa = $gurus->firstWhere('username', 'anissaramadani') ?? $gurus->first();
        $guruBetti = $gurus->firstWhere('username', 'bettisulisyowati') ?? $gurus->first();
        $guruFajar = $gurus->firstWhere('username', 'fajarsiswanto') ?? $gurus->first();

        $jadwalHarian = [
            'Senin' => [$guruBada->id],
            'Selasa' => [$guruAnissa->id],
            'Rabu' => [$guruBada->id, $guruAnissa->id],
            'Kamis' => [$guruBetti->id], // Hari ini: Khusus Bu Betti saja yang bertugas piket!
            'Jumat' => [$guruFajar->id],
            'Sabtu' => [$guruBetti->id],
        ];

        foreach ($jadwalHarian as $day => $userIds) {
            foreach ($userIds as $userId) {
                JadwalPiket::create([
                    'user_id' => $userId,
                    'hari' => $day,
                    'bulan' => date('n'),
                    'tahun' => date('Y'),
                    'shift' => 1,
                    'jam_mulai' => '07:00:00',
                    'jam_selesai' => '15:00:00',
                ]);
            }
        }
    }
}
