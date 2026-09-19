<?php

namespace Database\Seeders;

use App\Models\JadwalPiket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalPiketSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = base_path('scripts/master_data.json');
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
            $piketData = $data['piket'] ?? null;

            if ($piketData) {
                $allGurus = User::where('role', 'guru')->get();
                $guruLookup = [];
                foreach ($allGurus as $g) {
                    $clean = strtolower(preg_replace('/[^a-zA-Z]/', '', $g->name));
                    $guruLookup[$clean] = $g;
                }

                DB::table('jadwal_pikets')->truncate();
                $piketInserts = [];
                $now = now()->toDateTimeString();

                // A. Piket Waka
                foreach ($piketData['waka'] ?? [] as $w) {
                    $wClean = strtolower(preg_replace('/[^a-zA-Z]/', '', $w['nama']));
                    $guru = $guruLookup[$wClean] ?? null;
                    if ($guru) {
                        $piketInserts[] = [
                            'user_id' => $guru->id,
                            'hari' => $w['hari'],
                            'tipe' => 'waka',
                            'keterangan' => $w['keterangan'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                // B. Piket Minggu 1
                foreach ($piketData['minggu_1'] ?? [] as $p1) {
                    $pClean = strtolower(preg_replace('/[^a-zA-Z]/', '', $p1['nama']));
                    $guru = $guruLookup[$pClean] ?? null;
                    if ($guru) {
                        $keterangan = "{$p1['role']} Piket {$p1['shift']} (" . ($p1['shift'] === 'Pagi' ? '07.00 - 11.00' : '11.00 - 15.00') . ") - Minggu 1";
                        $piketInserts[] = [
                            'user_id' => $guru->id,
                            'hari' => $p1['hari'],
                            'tipe' => 'guru',
                            'keterangan' => $keterangan,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                // C. Piket Minggu 2
                foreach ($piketData['minggu_2'] ?? [] as $p2) {
                    $pClean = strtolower(preg_replace('/[^a-zA-Z]/', '', $p2['nama']));
                    $guru = $guruLookup[$pClean] ?? null;
                    if ($guru) {
                        $keterangan = "{$p2['role']} Piket {$p2['shift']} (" . ($p2['shift'] === 'Pagi' ? '07.00 - 11.00' : '11.00 - 15.00') . ") - Minggu 2";
                        $piketInserts[] = [
                            'user_id' => $guru->id,
                            'hari' => $p2['hari'],
                            'tipe' => 'guru',
                            'keterangan' => $keterangan,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                foreach (array_chunk($piketInserts, 50) as $chunk) {
                    DB::table('jadwal_pikets')->insert($chunk);
                }
                return;
            }
        }

        // Fallback default
        $piketUser = User::where('username', 'bettisulisyowati')->first() ?? User::where('role', 'guru')->first();
        $wakaUser = User::where('username', 'fajarsiswanto')->first() ?? User::where('role', 'guru')->skip(1)->first();

        if (!$piketUser) return;

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        foreach ($days as $day) {
            JadwalPiket::updateOrCreate(
                ['hari' => $day, 'tipe' => 'guru'],
                ['user_id' => $piketUser->id, 'keterangan' => "Petugas Piket Guru Hari {$day}"]
            );

            if ($wakaUser) {
                JadwalPiket::updateOrCreate(
                    ['hari' => $day, 'tipe' => 'waka'],
                    ['user_id' => $wakaUser->id, 'keterangan' => "Petugas Piket Waka Hari {$day}"]
                );
            }
        }
    }
}
