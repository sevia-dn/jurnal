<?php

namespace App\Imports;

use App\Models\Mapel;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;

    public int $updatedCount = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $nama = $row['nama'] ?? $row['nama_guru'] ?? $row['name'] ?? null;
            if (empty($nama)) {
                continue;
            }

            $nip = isset($row['nip']) && ! empty(trim((string) $row['nip'])) ? trim((string) $row['nip']) : null;
            $noHp = $row['no_hp'] ?? $row['nohp'] ?? $row['telepon'] ?? $row['no_telepon'] ?? null;
            $mapelInput = $row['mapel'] ?? $row['mata_pelajaran'] ?? $row['nama_mapel'] ?? null;

            $mapelId = null;
            if (! empty($mapelInput)) {
                $mapel = Mapel::where('nama_mapel', 'like', trim($mapelInput))
                    ->orWhere('kode_mapel', 'like', trim($mapelInput))
                    ->first();
                if (! $mapel) {
                    $clean = preg_replace('/[^A-Za-z]/', '', $mapelInput);
                    $prefix = strtoupper(substr($clean, 0, 4));
                    $kode = 'MP-'.($prefix ?: 'GEN').'-'.rand(100, 999);
                    $mapel = Mapel::create([
                        'kode_mapel' => $kode,
                        'nama_mapel' => trim($mapelInput),
                    ]);
                }
                $mapelId = $mapel->id;
            }

            // Cek apakah user dengan NIP sudah ada
            $existingUser = null;
            if ($nip) {
                $existingUser = User::where('nip', $nip)->first();
            }

            if ($existingUser) {
                $existingUser->update([
                    'name' => trim($nama),
                    'no_hp' => $noHp ?? $existingUser->no_hp,
                    'mapel_id' => $mapelId ?? $existingUser->mapel_id,
                ]);
                $this->updatedCount++;
            } else {
                $baseUsername = $nip ?: Str::slug($nama, '_');
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername.'_'.$counter;
                    $counter++;
                }

                $passwordText = $nip ?: 'guru123';

                User::create([
                    'name' => trim($nama),
                    'username' => $username,
                    'nip' => $nip,
                    'role' => 'guru',
                    'no_hp' => $noHp,
                    'mapel_id' => $mapelId,
                    'password' => Hash::make($passwordText),
                ]);
                $this->importedCount++;
            }
        }
    }
}
