<?php

namespace Tests\Feature;

use App\Models\JurnalMengajar;
use App\Models\KetidakhadiranGuru;
use App\Models\User;
use App\Services\JournalStatusService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalLatenessStatusTest extends TestCase
{
    use RefreshDatabase;

    private JournalStatusService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(JournalStatusService::class);
    }

    /**
     * Jurnal diisi pada hari jadwal -> tepat_waktu, is_late = false.
     */
    public function test_journal_submitted_same_day_is_tepat_waktu(): void
    {
        $date = '2026-10-06';
        $teacher = User::factory()->create(['role' => 'guru']);
        $jurnal = JurnalMengajar::factory()->create([
            'id_user' => $teacher->id,
            'tanggal' => $date,
            'is_late' => false,
            'filled_at' => Carbon::parse("{$date} 09:00:00", 'Asia/Jakarta'),
        ]);

        $result = $this->service->determineStatus($date, $jurnal);

        $this->assertSame(JournalStatusService::STATUS_TEPAT_WAKTU, $result['status']);
        $this->assertFalse($result['is_late']);
        $this->assertNotNull($result['filled_at']);
    }

    /**
     * Jurnal diisi setelah hari jadwal lewat -> terlambat, is_late = true.
     */
    public function test_journal_submitted_next_day_is_terlambat(): void
    {
        $date = '2026-10-05';
        $teacher = User::factory()->create(['role' => 'guru']);
        $jurnal = JurnalMengajar::factory()->create([
            'id_user' => $teacher->id,
            'tanggal' => $date,
            'is_late' => true,
            'late_mode' => 'los',
            'filled_at' => Carbon::parse('2026-10-06 08:00:00', 'Asia/Jakarta'),
        ]);

        $result = $this->service->determineStatus($date, $jurnal);

        $this->assertSame(JournalStatusService::STATUS_TERLAMBAT, $result['status']);
        $this->assertTrue($result['is_late']);
        $this->assertStringContainsString('Terlambat', $result['label']);
    }

    /**
     * Tidak ada jurnal, saat ini masih dalam tenggat H+1 23:59 -> belum_diisi.
     */
    public function test_no_journal_within_deadline_is_belum_diisi(): void
    {
        $date = '2026-10-06';
        // H+0 pagi: masih dalam tenggat (< H+1 23:59)
        $now = Carbon::parse("{$date} 10:00:00", 'Asia/Jakarta');

        $result = $this->service->determineStatus($date, null, null, $now);

        $this->assertSame(JournalStatusService::STATUS_BELUM_DIISI, $result['status']);
        $this->assertFalse($result['is_late']);
    }

    /**
     * Tidak ada jurnal, saat ini sudah melewati H+1 23:59 -> tidak_diisi.
     */
    public function test_no_journal_past_deadline_is_tidak_diisi(): void
    {
        $date = '2026-10-05';
        // H+2 pagi: sudah melewati tenggat H+1 pukul 23:59
        $now = Carbon::parse('2026-10-07 10:00:00', 'Asia/Jakarta');

        $result = $this->service->determineStatus($date, null, null, $now);

        $this->assertSame(JournalStatusService::STATUS_TIDAK_DIISI, $result['status']);
    }

    /**
     * Guru memiliki ketidakhadiran yang valid -> guru_tidak_masuk.
     */
    public function test_valid_absence_results_in_guru_tidak_masuk(): void
    {
        $date = '2026-10-06';
        $teacher = User::factory()->create(['role' => 'guru']);
        $absence = KetidakhadiranGuru::create([
            'user_id' => $teacher->id,
            'tanggal' => $date,
            'alasan' => 'sakit',
            'status' => 'disetujui',
        ]);

        $result = $this->service->determineStatus($date, null, $absence);

        $this->assertSame(JournalStatusService::STATUS_GURU_TIDAK_MASUK, $result['status']);
        $this->assertStringContainsString('Sakit', $result['label']);
    }

    /**
     * Ketidakhadiran yang ditolak tidak dihitung -> jatuh ke tidak_diisi.
     */
    public function test_rejected_absence_is_not_counted_as_guru_tidak_masuk(): void
    {
        $date = '2026-10-05';
        $teacher = User::factory()->create(['role' => 'guru']);
        $absence = KetidakhadiranGuru::create([
            'user_id' => $teacher->id,
            'tanggal' => $date,
            'alasan' => 'izin',
            'status' => 'ditolak',
        ]);

        // H+2: past deadline
        $now = Carbon::parse('2026-10-07 10:00:00', 'Asia/Jakarta');

        $result = $this->service->determineStatus($date, null, $absence, $now);

        $this->assertSame(JournalStatusService::STATUS_TIDAK_DIISI, $result['status']);
    }
}
