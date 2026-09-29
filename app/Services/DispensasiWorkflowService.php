<?php

namespace App\Services;

use App\Models\Dispensasi;
use App\Models\JadwalMengajar;
use App\Models\Notifikasi;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DispensasiWorkflowService
{
    public function __construct(private StudentAttendanceSynchronizationService $attendanceSynchronizationService) {}

    public function approve(Dispensasi $dispensasi): void
    {
        $dispensasi->loadMissing(['siswa.kelas', 'siswas.kelas']);

        if (blank($dispensasi->token_verifikasi)) {
            $dispensasi->update(['token_verifikasi' => Str::random(40)]);
        }

        // Gunakan semua siswa dari pivot (multi-siswa), fallback ke siswa utama jika pivot kosong
        $allSiswas = $dispensasi->siswas->isNotEmpty()
            ? $dispensasi->siswas
            : collect([$dispensasi->siswa])->filter();

        foreach ($allSiswas as $siswa) {
            foreach ($this->markPiketKehadiranAsDispensasiForSiswa($dispensasi, $siswa) as $attendance) {
                $this->attendanceSynchronizationService->synchronize(
                    $attendance,
                    $dispensasi->jam_ke_mulai,
                    $dispensasi->jam_ke_selesai,
                );
            }
        }

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

    /**
     * @return array<int, PiketKehadiranSiswa>
     */
    private function markPiketKehadiranAsDispensasiForSiswa(Dispensasi $dispensasi, Siswa $siswa): array
    {
        $startDate = Carbon::parse($dispensasi->tanggal);
        $endDate = Carbon::parse($dispensasi->tanggal_selesai);

        $attendances = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $attendances[] = PiketKehadiranSiswa::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'tanggal' => $date->toDateString(),
                ],
                [
                    'kelas_id' => $siswa->kelas_id,
                    'status' => 'D',
                    'sumber' => PiketKehadiranSiswa::SumberDispensasiWaka,
                    'catatan' => 'Dispensasi disetujui: '.$dispensasi->alasan,
                    'dicatat_oleh' => $dispensasi->diproses_oleh ?? $dispensasi->dibuat_oleh,
                ]
            );
        }

        return $attendances;
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
