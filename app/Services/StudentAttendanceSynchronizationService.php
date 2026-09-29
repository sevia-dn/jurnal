<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\JurnalMengajar;
use App\Models\PiketKehadiranSiswa;

class StudentAttendanceSynchronizationService
{
    /**
     * Terapkan catatan piket pada semua logbook kelas yang telah ada di hari
     * sama. Ini membuat S/I/D dari piket langsung tampak pada logbook guru.
     */
    public function synchronize(PiketKehadiranSiswa $attendance, ?int $jamKeMulai = null, ?int $jamKeSelesai = null): void
    {
        $journalsQuery = JurnalMengajar::query()
            ->where('id_kelas', $attendance->kelas_id)
            ->whereDate('tanggal', $attendance->tanggal);

        if ($jamKeMulai !== null) {
            $journalsQuery
                ->where('jam_ke', '>=', $jamKeMulai)
                ->where('jam_ke', '<=', $jamKeSelesai ?? $jamKeMulai);
        }

        $journals = $journalsQuery->get();

        foreach ($journals as $journal) {
            Absensi::updateOrCreate(
                ['id_jurnal' => $journal->id_jurnal, 'id_siswa' => $attendance->siswa_id],
                [
                    'status' => $this->normalizeStatus($attendance->status),
                    'catatan' => $this->automaticNote($attendance),
                ],
            );

            $this->refreshJournalAttendanceSummary($journal);
        }
    }

    public function normalizeStatus(string $status): string
    {
        return match (mb_strtolower(trim($status))) {
            's', 'sakit' => 'Sakit',
            'i', 'izin' => 'Izin',
            'd', 'dispensasi' => 'D',
            'a', 'alfa', 'alpa' => 'Alpa',
            default => 'Hadir',
        };
    }

    public function automaticNote(PiketKehadiranSiswa $attendance): string
    {
        $source = $attendance->sumber === PiketKehadiranSiswa::SumberDispensasiWaka
            ? 'Dispensasi Waka'
            : 'Guru Piket';
        $note = trim((string) $attendance->catatan);

        return "Otomatis dari {$source}".($note !== '' ? ": {$note}" : '.');
    }

    private function refreshJournalAttendanceSummary(JurnalMengajar $journal): void
    {
        $summary = $journal->absensis()
            ->get(['status'])
            ->groupBy('status')
            ->map->count();

        $hadir = $summary->get('Hadir', 0);
        $sakit = $summary->get('Sakit', 0);
        $izin = $summary->get('Izin', 0);
        $alpa = $summary->get('Alpa', 0);
        $dispensasi = $summary->get('D', 0);

        $journal->update([
            'jumlah_hadir' => $hadir,
            'jumlah_sakit' => $sakit,
            'jumlah_izin' => $izin,
            'jumlah_alpa' => $alpa,
            'jumlah_dispensasi' => $dispensasi,
            'jumlah_tidak_hadir' => $sakit + $izin + $alpa + $dispensasi,
        ]);
    }
}
