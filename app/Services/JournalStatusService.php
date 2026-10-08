<?php

namespace App\Services;

use App\Models\JurnalMengajar;
use App\Models\KetidakhadiranGuru;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class JournalStatusService
{
    public const STATUS_TEPAT_WAKTU = 'tepat_waktu';

    public const STATUS_TERLAMBAT = 'terlambat';

    public const STATUS_BELUM_DIISI = 'belum_diisi';

    public const STATUS_TIDAK_DIISI = 'tidak_diisi';

    public const STATUS_GURU_TIDAK_MASUK = 'guru_tidak_masuk';

    /**
     * Tentukan status jurnal mengajar berdasarkan prioritas:
     * 1. Guru tidak masuk (izin/sakit/dinas/alpha terkonfirmasi)
     * 2. Sudah diisi (tepat waktu / terlambat)
     * 3. Belum diisi (jadwal aktif s.d H+1 23:59:59 WIB)
     * 4. Tidak diisi (melewati H+1 23:59:59 WIB)
     *
     * @return array{status: string, label: string, short_label: string, badge_class: string, icon: string, filled_at: ?CarbonInterface, is_late: bool, reason: ?string}
     */
    public function determineStatus(
        string|CarbonInterface $scheduleDate,
        ?JurnalMengajar $jurnal = null,
        ?KetidakhadiranGuru $teacherAbsence = null,
        ?CarbonInterface $currentTime = null,
    ): array {
        Carbon::setLocale('id');
        $now = $currentTime
            ? Carbon::parse($currentTime, 'Asia/Jakarta')
            : Carbon::now('Asia/Jakarta');

        $scheduleDateObj = Carbon::parse($scheduleDate, 'Asia/Jakarta')->startOfDay();
        $scheduleDateString = $scheduleDateObj->toDateString();

        // 1. Prioritas Utama: Guru Tidak Masuk
        if ($teacherAbsence !== null && $this->isAbsenceValid($teacherAbsence)) {
            $alasan = ucfirst($teacherAbsence->alasan ?: 'Izin');

            return [
                'status' => self::STATUS_GURU_TIDAK_MASUK,
                'label' => "Guru Tidak Masuk ({$alasan})",
                'short_label' => "Tidak Masuk ({$alasan})",
                'badge_class' => 'bg-slate-100 text-slate-800 border-slate-300',
                'icon' => 'bi-person-x-fill',
                'filled_at' => $jurnal?->filled_at,
                'is_late' => (bool) ($jurnal?->is_late),
                'reason' => $teacherAbsence->keterangan ?: $alasan,
            ];
        }

        // 2. Sudah diisi (Tepat Waktu / Terlambat)
        if ($jurnal !== null) {
            $isLate = (bool) $jurnal->is_late;
            $filledAt = $jurnal->filled_at ? Carbon::parse($jurnal->filled_at, 'Asia/Jakarta') : null;

            if ($isLate) {
                $timeText = $filledAt ? $filledAt->translatedFormat('d M Y H:i') : $scheduleDateObj->translatedFormat('d M Y');

                return [
                    'status' => self::STATUS_TERLAMBAT,
                    'label' => "Terlambat, diisi {$timeText}",
                    'short_label' => 'Terlambat',
                    'badge_class' => 'bg-amber-100 text-amber-800 border-amber-300',
                    'icon' => 'bi-clock-history',
                    'filled_at' => $filledAt,
                    'is_late' => true,
                    'reason' => null,
                ];
            }

            return [
                'status' => self::STATUS_TEPAT_WAKTU,
                'label' => 'Tepat Waktu',
                'short_label' => 'Tepat Waktu',
                'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'icon' => 'bi-check2-circle',
                'filled_at' => $filledAt,
                'is_late' => false,
                'reason' => null,
            ];
        }

        // 3. Batas toleransi pengisian: sampai H+1 pukul 23:59:59 WIB
        $deadlineHPlus1 = $scheduleDateObj->copy()->addDay()->endOfDay();

        if ($now->lte($deadlineHPlus1)) {
            return [
                'status' => self::STATUS_BELUM_DIISI,
                'label' => 'Belum Diisi',
                'short_label' => 'Belum Diisi',
                'badge_class' => 'bg-sky-100 text-sky-800 border-sky-300',
                'icon' => 'bi-clock',
                'filled_at' => null,
                'is_late' => false,
                'reason' => null,
            ];
        }

        // 4. Melewati H+1 pukul 23:59:59 WIB tanpa pengisian: Tidak Diisi
        return [
            'status' => self::STATUS_TIDAK_DIISI,
            'label' => 'Tidak Diisi',
            'short_label' => 'Tidak Diisi',
            'badge_class' => 'bg-rose-100 text-rose-800 border-rose-300',
            'icon' => 'bi-x-circle',
            'filled_at' => null,
            'is_late' => false,
            'reason' => null,
        ];
    }

    /**
     * Cek apakah rekaman ketidakhadiran guru valid dan tidak ditolak.
     */
    private function isAbsenceValid(KetidakhadiranGuru $absence): bool
    {
        if (isset($absence->status_konfirmasi_waka) && $absence->status_konfirmasi_waka === 'ditolak') {
            return false;
        }

        if ($absence->status === 'ditolak') {
            return false;
        }

        return true;
    }
}
