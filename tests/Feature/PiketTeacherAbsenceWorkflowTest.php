<?php

namespace Tests\Feature;

use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\KehadiranGuru;
use App\Models\Kelas;
use App\Models\KetidakhadiranGuru;
use App\Models\Mapel;
use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PiketTeacherAbsenceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_piket_notification_opens_the_related_absence_request_and_pending_absence_is_shown(): void
    {
        $date = '2026-09-30';
        $this->travelTo(Carbon::parse("{$date} 08:00:00", 'Asia/Jakarta'));
        $teacher = User::factory()->create(['role' => 'guru']);
        $piket = User::factory()->create(['role' => 'guru']);
        $this->schedulePiket($piket, $date);

        $this->actingAs($teacher)
            ->post(route('guru.ketidakhadiran.store'), [
                'tanggal' => $date,
                'alasan' => 'izin',
                'keterangan' => 'Ada keperluan keluarga.',
            ])
            ->assertRedirect(route('guru.utama'));

        $absence = KetidakhadiranGuru::where('user_id', $teacher->id)->firstOrFail();
        $notification = Notifikasi::where('id_user', $piket->id)->firstOrFail();
        $this->assertSame($absence->id, $notification->id_ketidakhadiran_guru);

        $this->actingAs($piket)
            ->post(route('guru.notifikasi.read', $notification))
            ->assertRedirect(route('piket.ketidakhadiran-guru.show', $absence));
        $this->actingAs($piket)
            ->get(route('piket.ketidakhadiran-guru.show', $absence))
            ->assertOk()
            ->assertSee($teacher->name)
            ->assertSee('Ada keperluan keluarga.');
        $this->actingAs($piket)
            ->get(route('piket.kehadiran', ['tanggal' => $date]))
            ->assertOk()
            ->assertSee('Menunggu Persetujuan Izin Guru')
            ->assertSee($teacher->name)
            ->assertSee('Izin')
            ->assertSee('menunggu persetujuan piket');
    }

    public function test_approved_absence_is_not_overwritten_as_present_by_a_teacher_attendance_record(): void
    {
        $date = '2026-09-30';
        $this->travelTo(Carbon::parse("{$date} 08:00:00", 'Asia/Jakarta'));
        $teacher = User::factory()->create(['role' => 'guru']);
        $piket = User::factory()->create(['role' => 'guru']);
        $this->schedulePiket($piket, $date);
        KetidakhadiranGuru::create([
            'user_id' => $teacher->id,
            'tanggal' => $date,
            'alasan' => 'sakit',
            'status' => 'disetujui',
        ]);
        KehadiranGuru::create([
            'user_id' => $teacher->id,
            'tanggal' => $date,
            'status' => 'Hadir',
        ]);

        $this->actingAs($piket)
            ->get(route('piket.kehadiran', ['tanggal' => $date]))
            ->assertOk()
            ->assertSee($teacher->name)
            ->assertSee('Sakit')
            ->assertSee('Pengajuan guru disetujui');
    }

    public function test_approved_sick_teacher_who_submits_a_journal_is_not_counted_twice_on_the_piket_dashboard(): void
    {
        $date = '2026-09-30';
        $this->travelTo(Carbon::parse("{$date} 08:00:00", 'Asia/Jakarta'));
        $teacher = User::factory()->create(['role' => 'guru']);
        $piket = User::factory()->create(['role' => 'guru']);
        $this->schedulePiket($piket, $date);
        $kelas = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 0]);
        $mapel = Mapel::create(['kode_mapel' => 'INF', 'nama_mapel' => 'Informatika']);
        KetidakhadiranGuru::create([
            'user_id' => $teacher->id,
            'tanggal' => $date,
            'alasan' => 'sakit',
            'status' => 'disetujui',
        ]);
        JurnalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => $date,
            'jam_ke' => 1,
            'materi' => 'Tugas mandiri',
            'status_validasi' => 'belum_divalidasi',
        ]);

        $this->actingAs($piket)
            ->get(route('dashboard.piket'))
            ->assertOk()
            ->assertSee('0 Hadir · 0 Izin · 1 Sakit');
    }

    public function test_repeated_absence_submission_does_not_create_duplicate_notifications_for_piket(): void
    {
        $date = '2026-09-30';
        $this->travelTo(Carbon::parse("{$date} 08:00:00", 'Asia/Jakarta'));
        $teacher = User::factory()->create(['role' => 'guru']);
        $piket = User::factory()->create(['role' => 'guru']);
        $this->schedulePiket($piket, $date);

        // Submit first time
        $this->actingAs($teacher)
            ->post(route('guru.ketidakhadiran.store'), [
                'tanggal' => $date,
                'alasan' => 'izin',
                'keterangan' => 'Ada keperluan keluarga.',
            ])
            ->assertRedirect(route('guru.utama'));

        // Submit second time (e.g. double click or update)
        $this->actingAs($teacher)
            ->post(route('guru.ketidakhadiran.store'), [
                'tanggal' => $date,
                'alasan' => 'izin',
                'keterangan' => 'Ada keperluan keluarga diperbarui.',
            ])
            ->assertRedirect(route('guru.utama'));

        // Assert exactly 1 notification is created for this piket user
        $piketNotifCount = Notifikasi::where('id_user', $piket->id)
            ->where('tipe', 'guru_tidak_hadir')
            ->count();

        $this->assertSame(1, $piketNotifCount);
    }

    private function schedulePiket(User $piket, string $date): void
    {
        JadwalPiket::create([
            'user_id' => $piket->id,
            'tanggal' => $date,
            'hari' => 'Rabu',
            'tipe' => 'guru',
            'shift' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '12:00:00',
        ]);
    }
}
