<?php

namespace App\Services;

use App\Http\Controllers\GuruController;
use App\Models\PengaturanJurnal;
use Carbon\Carbon;

class ValidasiTenggatJurnalService
{
    /**
     * Memvalidasi apakah pengisian jurnal diizinkan berdasarkan kebijakan tenggat yang aktif.
     *
     * @param string $tanggal Format Y-m-d
     * @param int|null $jamMulai Jam ke- mulai mengajar (1-13)
     * @param int|null $jamSelesai Jam ke- selesai mengajar (1-13)
     * @param string|null $hari Nama hari (e.g. 'Senin')
     * @return array{isValid: bool, message: string|null, kebijakan: string}
     */
    public static function validasi(string $tanggal, ?int $jamMulai = null, ?int $jamSelesai = null, ?string $hari = null): array
    {
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $yesterdayDate = $now->copy()->subDay()->toDateString();
        $currentTime = $now->format('H:i');

        $pengaturan = PengaturanJurnal::getKebijakanAktif();
        $kebijakan = $pengaturan->kebijakan_tenggat ?? 'jam_mengajar';

        // 1. Tanggal di masa depan selalu ditolak untuk semua kebijakan
        if ($tanggal > $todayDate) {
            return [
                'isValid' => false,
                'message' => 'Pengisian jurnal untuk tanggal di masa depan tidak diperbolehkan.',
                'kebijakan' => $kebijakan,
            ];
        }

        // 2. Kebijakan Longgar: Boleh hari ini atau H-1 (kemarin)
        if ($kebijakan === 'longgar') {
            if ($tanggal !== $todayDate && $tanggal !== $yesterdayDate) {
                return [
                    'isValid' => false,
                    'message' => 'Kebijakan tenggat longgar hanya memperbolehkan pengisian jurnal untuk hari ini dan toleransi keterlambatan maksimal H-1 (kemarin).',
                    'kebijakan' => $kebijakan,
                ];
            }

            return [
                'isValid' => true,
                'message' => null,
                'kebijakan' => $kebijakan,
            ];
        }

        // 3. Kebijakan Hari Ini: Boleh kapan saja sepanjang hari ini (00:00 - 23:59)
        if ($kebijakan === 'hari_ini') {
            if ($tanggal !== $todayDate) {
                return [
                    'isValid' => false,
                    'message' => 'Kebijakan tenggat hari ini hanya memperbolehkan pengisian jurnal pada tanggal hari ini (00:00 - 23:59 WIB).',
                    'kebijakan' => $kebijakan,
                ];
            }

            return [
                'isValid' => true,
                'message' => null,
                'kebijakan' => $kebijakan,
            ];
        }

        // 4. Kebijakan Default (jam_mengajar): Hanya saat sesi jam mengajar berlangsung hari ini
        if ($tanggal !== $todayDate) {
            return [
                'isValid' => false,
                'message' => 'Kebijakan tenggat jam mengajar mewajibkan pengisian jurnal tepat pada tanggal hari ini dan saat jam mengajar berlangsung.',
                'kebijakan' => $kebijakan,
            ];
        }

        if ($jamMulai !== null) {
            $hariNama = $hari ?? $now->translatedFormat('l');
            $jamAkhir = $jamSelesai ?: $jamMulai;

            $slotMulai = GuruController::getJamSlot($hariNama, $jamMulai);
            $slotSelesai = GuruController::getJamSlot($hariNama, $jamAkhir);

            $startSlot = $slotMulai['start'];
            $endSlot = $slotSelesai['end'];

            if ($currentTime < $startSlot) {
                return [
                    'isValid' => false,
                    'message' => "Jam pelajaran untuk sesi ini belum dimulai ({$startSlot} - {$endSlot} WIB). Anda hanya dapat mengisi jurnal setelah jam pelajaran dimulai.",
                    'kebijakan' => $kebijakan,
                ];
            }

            if ($currentTime > $endSlot) {
                return [
                    'isValid' => false,
                    'message' => "Batas waktu pengisian jurnal untuk sesi ini ({$startSlot} - {$endSlot} WIB) telah terlewat. Anda tidak dapat mengisi jurnal setelah jam mengajar berakhir.",
                    'kebijakan' => $kebijakan,
                ];
            }
        }

        return [
            'isValid' => true,
            'message' => null,
            'kebijakan' => $kebijakan,
        ];
    }
}
