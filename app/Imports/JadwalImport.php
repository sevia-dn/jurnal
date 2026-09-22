<?php

namespace App\Imports;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JadwalImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;

    public int $updatedCount = 0;

    public function collection(Collection $rows): void
    {
        $defaultGuru = User::where('role', 'guru')->first() ?? User::where('role', 'admin')->first();

        foreach ($rows as $row) {
            $hariInput = $row['hari'] ?? null;
            $kelasInput = $row['kelas'] ?? $row['nama_kelas'] ?? null;
            $mapelInput = $row['mapel'] ?? $row['mata_pelajaran'] ?? $row['nama_mapel'] ?? null;

            if (empty($hariInput) || empty($kelasInput) || empty($mapelInput)) {
                continue;
            }

            // Normalisasi hari
            $hariValid = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $hariClean = ucfirst(strtolower(trim($hariInput)));
            if (! in_array($hariClean, $hariValid)) {
                $hariClean = 'Senin';
            }

            // Cari atau buat Kelas
            $kelas = Kelas::where('nama_kelas', trim($kelasInput))->first();
            if (! $kelas) {
                $kelas = Kelas::create([
                    'nama_kelas' => trim($kelasInput),
                    'wali_kelas' => null,
                    'jumlah_siswa' => 0,
                ]);
            }

            // Cari atau buat Mapel
            $mapel = Mapel::where('nama_mapel', trim($mapelInput))->first();
            if (! $mapel) {
                $clean = preg_replace('/[^A-Za-z]/', '', $mapelInput);
                $prefix = strtoupper(substr($clean, 0, 4));
                $kode = 'MP-'.($prefix ?: 'GEN').'-'.rand(100, 999);
                $mapel = Mapel::create([
                    'kode_mapel' => $kode,
                    'nama_mapel' => trim($mapelInput),
                ]);
            }

            // Cari Guru
            $guruInput = $row['guru'] ?? $row['nip'] ?? $row['nama_guru'] ?? null;
            $guru = null;
            if (! empty($guruInput)) {
                $guru = User::where(function ($q) use ($guruInput) {
                    $q->where('nip', trim((string) $guruInput))
                        ->orWhere('name', 'like', '%'.trim($guruInput).'%')
                        ->orWhere('username', trim((string) $guruInput));
                })->first();
            }
            if (! $guru) {
                $guru = $defaultGuru;
            }

            $jamKe = isset($row['jam_ke']) ? (int) $row['jam_ke'] : 1;

            // Format waktu jam_mulai & jam_selesai
            $jamMulai = $row['jam_mulai'] ?? '07:00';
            $jamSelesai = $row['jam_selesai'] ?? '08:30';

            // Normalisasi time string H:i:s
            $jamMulaiStr = date('H:i:s', strtotime($jamMulai));
            $jamSelesaiStr = date('H:i:s', strtotime($jamSelesai));

            // Cek apakah jadwal di kelas, hari, dan jam_ke yang sama sudah ada
            $existing = JadwalPelajaran::where('id_kelas', $kelas->id_kelas)
                ->where('hari', $hariClean)
                ->where('jam_ke', $jamKe)
                ->first();

            if ($existing) {
                $existing->update([
                    'id_user' => $guru->id,
                    'id_mapel' => $mapel->id,
                    'mapel' => $mapel->nama_mapel,
                    'jam_mulai' => $jamMulaiStr,
                    'jam_selesai' => $jamSelesaiStr,
                ]);
                $this->updatedCount++;
            } else {
                JadwalPelajaran::create([
                    'id_user' => $guru->id,
                    'id_kelas' => $kelas->id_kelas,
                    'id_mapel' => $mapel->id,
                    'hari' => $hariClean,
                    'jam_ke' => $jamKe,
                    'jam_mulai' => $jamMulaiStr,
                    'jam_selesai' => $jamSelesaiStr,
                    'mapel' => $mapel->nama_mapel,
                ]);
                $this->importedCount++;
            }
        }
    }
}
