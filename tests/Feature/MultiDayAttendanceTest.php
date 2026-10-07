<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\PeriodeKetidakhadiranSiswa;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use App\Services\MultiDayAttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiDayAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_applies_multi_day_attendance_and_creates_records_skipping_weekends(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII-RPL-1', 'jumlah_siswa' => 30]);
        $siswa = Siswa::create([
            'nama' => 'Budi Santoso',
            'nis' => '12345',
            'kelas_id' => $kelas->id_kelas,
            'jenis_kelamin' => 'L',
        ]);
        $piket = User::factory()->create(['role' => 'piket']);

        $service = app(MultiDayAttendanceService::class);

        // 2026-10-05 (Monday) to 2026-10-09 (Friday) = 5 days (all weekdays)
        $periode = $service->applyMultiDayAttendance(
            siswa: $siswa,
            startDate: '2026-10-05',
            endDate: '2026-10-09',
            status: 'Sakit',
            alasan: 'Rawat inap di rumah sakit',
            user: $piket,
            skipWeekends: true
        );

        $this->assertInstanceOf(PeriodeKetidakhadiranSiswa::class, $periode);
        $this->assertSame('Sakit', $periode->status);
        $this->assertSame(5, $periode->attendanceRecords()->count());

        $records = PiketKehadiranSiswa::where('siswa_id', $siswa->id)->get();
        $this->assertCount(5, $records);
        foreach ($records as $r) {
            $this->assertSame('Sakit', $r->status);
            $this->assertTrue((bool) $r->is_multi_day);
            $this->assertSame($periode->id, $r->periode_id);
        }
    }

    public function test_weekend_days_are_skipped_when_recording_multi_day_absence(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII-RPL-1', 'jumlah_siswa' => 30]);
        $siswa = Siswa::create([
            'nama' => 'Siti Nurhaliza',
            'nis' => '12346',
            'kelas_id' => $kelas->id_kelas,
            'jenis_kelamin' => 'P',
        ]);
        $piket = User::factory()->create(['role' => 'piket']);

        $service = app(MultiDayAttendanceService::class);

        // 2026-10-09 (Friday) to 2026-10-12 (Monday) = 4 calendar days (Fri, Sat, Sun, Mon)
        // With skipWeekends = true, only Friday & Monday should have records (2 days)
        $periode = $service->applyMultiDayAttendance(
            siswa: $siswa,
            startDate: '2026-10-09',
            endDate: '2026-10-12',
            status: 'Izin',
            alasan: 'Acara keluarga di luar kota',
            user: $piket,
            skipWeekends: true
        );

        $records = PiketKehadiranSiswa::where('siswa_id', $siswa->id)->orderBy('tanggal')->get();
        $this->assertCount(2, $records);
        $this->assertSame('2026-10-09', $records[0]->tanggal->toDateString());
        $this->assertSame('2026-10-12', $records[1]->tanggal->toDateString());
    }

    public function test_piket_can_submit_multi_day_absence_via_http_endpoint(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII-RPL-1', 'jumlah_siswa' => 30]);
        $siswa = Siswa::create([
            'nama' => 'Ahmad Dahlan',
            'nis' => '12347',
            'kelas_id' => $kelas->id_kelas,
            'jenis_kelamin' => 'L',
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('piket.kehadiran-siswa.multi-day'), [
                'siswa_id' => $siswa->id,
                'tanggal_mulai' => '2026-10-05',
                'tanggal_selesai' => '2026-10-07',
                'status' => 'Sakit',
                'alasan' => 'Flu berat dan istirahat dokter.',
            ])
            ->assertRedirect();

        $periode = PeriodeKetidakhadiranSiswa::where('siswa_id', $siswa->id)->firstOrFail();
        $this->assertSame('Sakit', $periode->status);
        $this->assertSame('2026-10-05', $periode->tanggal_mulai->toDateString());
        $this->assertSame('2026-10-07', $periode->tanggal_selesai->toDateString());

        $this->assertSame(3, PiketKehadiranSiswa::where('siswa_id', $siswa->id)->count());
    }

    public function test_piket_can_cancel_multi_day_absence_period(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'XII-RPL-1', 'jumlah_siswa' => 30]);
        $siswa = Siswa::create([
            'nama' => 'Dewi Sartika',
            'nis' => '12348',
            'kelas_id' => $kelas->id_kelas,
            'jenis_kelamin' => 'P',
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $service = app(MultiDayAttendanceService::class);
        $periode = $service->applyMultiDayAttendance(
            siswa: $siswa,
            startDate: '2026-10-05',
            endDate: '2026-10-07',
            status: 'Izin',
            user: $admin
        );

        $this->assertSame(3, PiketKehadiranSiswa::where('siswa_id', $siswa->id)->count());

        $this->actingAs($admin)
            ->delete(route('piket.kehadiran-siswa.multi-day.cancel', $periode))
            ->assertRedirect();

        $this->assertDatabaseMissing('periode_ketidakhadiran_siswas', ['id' => $periode->id]);
        $this->assertSame(0, PiketKehadiranSiswa::where('siswa_id', $siswa->id)->count());
    }
}
