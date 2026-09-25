<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Dispensasi;
use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\Notifikasi;
use App\Models\PiketKehadiranSiswa;
use App\Models\User;
use Carbon\Carbon;

class DispensasiWorkflowService
{
    public function approve(Dispensasi $dispensasi): void
    {
        $dispensasi->loadMissing('siswa.kelas');

        $this->markExistingAttendanceAsDispensasi($dispensasi);
        $this->markPiketKehadiranAsDispensasi($dispensasi);
        $this->notifyRelatedUsers($dispensasi);
    }

    public function isActiveForStudentAt(Dispensasi $dispensasi, string $date, int $jamKe): bool
    {
        if ($dispensasi->status_akhir !== 'disetujui') {
            return false;
        }

        if ($date < $dispensasi->tanggal->toDateString() || $date > $dispensasi->tanggal_selesai->toDateString()) {
            return false;
        }

        if (! $dispensasi->jam_ke_mulai) {
            return true;
        }

        return $jamKe >= $dispensasi->jam_ke_mulai
            && $jamKe <= ($dispensasi->jam_ke_selesai ?? $dispensasi->jam_ke_mulai);
    }

    private function markExistingAttendanceAsDispensasi(Dispensasi $dispensasi): void
    {
        $jurnals = JurnalMengajar::query()
            ->where('id_kelas', $dispensasi->siswa->kelas_id)
            ->whereBetween('tanggal', [$dispensasi->tanggal->toDateString(), $dispensasi->tanggal_selesai->toDateString()])
            ->get();

        foreach ($jurnals as $jurnal) {
            if (! $this->isActiveForStudentAt($dispensasi, Carbon::parse($jurnal->tanggal)->toDateString(), $jurnal->jam_ke)) {
                continue;
            }

            Absensi::updateOrCreate(
                ['id_jurnal' => $jurnal->id_jurnal, 'id_siswa' => $dispensasi->siswa_id],
                ['status' => 'D', 'catatan' => 'Dispensasi disetujui: '.$dispensasi->alasan],
            );
        }
    }

    private function markPiketKehadiranAsDispensasi(Dispensasi $dispensasi): void
    {
        $startDate = Carbon::parse($dispensasi->tanggal);
        $endDate = Carbon::parse($dispensasi->tanggal_selesai);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            PiketKehadiranSiswa::updateOrCreate(
                [
                    'siswa_id' => $dispensasi->siswa_id,
                    'tanggal' => $date->toDateString(),
                ],
                [
                    'kelas_id' => $dispensasi->siswa->kelas_id,
                    'status' => 'D',
                    'catatan' => 'Dispensasi disetujui: '.$dispensasi->alasan,
                    'dicatat_oleh' => $dispensasi->diproses_oleh ?? $dispensasi->dibuat_oleh,
                ]
            );
        }
    }

    private function notifyRelatedUsers(Dispensasi $dispensasi): void
    {
        $date = Carbon::parse($dispensasi->tanggal)->locale('id')->translatedFormat('l');
        $teacherIds = JadwalMengajar::query()
            ->where('id_kelas', $dispensasi->siswa->kelas_id)
            ->where('hari', $date)
            ->pluck('id_user');

        $namaKelasSiswa = $dispensasi->siswa->kelas?->nama_kelas;
        $pengurusIds = User::query()
            ->where('role', 'pengurus_kelas')
            ->get()
            ->filter(fn (User $u) => trim(str_ireplace('Pengurus Kelas ', '', $u->name)) === $namaKelasSiswa || $u->name === $namaKelasSiswa)
            ->pluck('id');

        $piketIds = User::query()
            ->whereHas('jadwalPikets', fn ($query) => $query->whereDate('tanggal', $dispensasi->tanggal))
            ->pluck('id');

        $recipientIds = $teacherIds
            ->merge($piketIds)
            ->merge($pengurusIds)
            ->push($dispensasi->dibuat_oleh)
            ->filter()
            ->unique();

        $kelas = $dispensasi->siswa->kelas?->nama_kelas ?? '-';
        $message = "{$dispensasi->siswa->nama} kelas {$kelas} mendapat dispensasi (D) pada {$dispensasi->deskripsi_waktu}. Alasan: {$dispensasi->alasan}";

        $recipientIds->each(function (int $userId) use ($dispensasi, $message): void {
            Notifikasi::updateOrCreate(
                ['id_user' => $userId, 'id_dispensasi' => $dispensasi->id],
                [
                    'id_kelas' => $dispensasi->siswa->kelas_id,
                    'judul' => 'Dispensasi siswa disetujui',
                    'pesan' => $message,
                    'tipe' => 'dispensasi',
                    'is_read' => false,
                ],
            );
        });
    }
}
