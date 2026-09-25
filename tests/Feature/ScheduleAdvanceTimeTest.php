<?php

namespace Tests\Feature;

use App\Models\JadwalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengaturan;
use App\Models\User;
use App\Services\ScheduleTimeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ScheduleAdvanceTimeTest extends TestCase
{
    use RefreshDatabase;

    private string $settingsPath;

    private bool $hadSettingsFile;

    private string|false $originalSettings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingsPath = storage_path('app/pengaturan.json');
        $this->hadSettingsFile = file_exists($this->settingsPath);
        $this->originalSettings = $this->hadSettingsFile ? file_get_contents($this->settingsPath) : false;
    }

    protected function tearDown(): void
    {
        if ($this->hadSettingsFile) {
            file_put_contents($this->settingsPath, $this->originalSettings);
        } elseif (file_exists($this->settingsPath)) {
            unlink($this->settingsPath);
        }

        parent::tearDown();
    }

    public function test_advanced_monday_schedule_updates_admin_data_and_teacher_schedule(): void
    {
        $this->travelTo(Carbon::parse('2026-09-21 07:05:00', 'Asia/Jakarta'));
        [$admin, $teacher, $lesson] = $this->createMondaySchedule();

        $this->actingAs($admin)
            ->post(route('dashboard.jadwal.shift-time'), [
                'hari' => 'Senin',
                'mode' => 'maju',
                'minutes' => 40,
            ])
            ->assertRedirect(route('dashboard.jadwal', ['kelas' => $lesson->id_kelas, 'hari' => 'Senin']));

        $lesson->refresh();

        $this->assertSame('07:00:00', $lesson->jam_mulai);
        $this->assertSame('07:40:00', $lesson->jam_selesai);
        $this->assertSame(1, Pengaturan::getValue('senin_is_maju'));
        $this->assertSame(['start' => '07:00', 'end' => '07:40'], app(ScheduleTimeService::class)->slot('Senin', 2));

        $this->actingAs($teacher)
            ->get(route('guru'))
            ->assertSee('07:00 - 07:40');

        Storage::fake('public');

        $this->actingAs($teacher)
            ->post(route('guru.jurnal.store'), [
                'id_kelas' => $lesson->id_kelas,
                'id_mapel' => $lesson->id_mapel,
                'jam_ke' => 2,
                'materi' => 'Materi setelah jam maju',
                'ada_tugas' => 'Tidak',
                'lampiran' => UploadedFile::fake()->image('bukti.jpg'),
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('jurnal_mengajars', [
            'id_user' => $teacher->id,
            'id_kelas' => $lesson->id_kelas,
            'id_mapel' => $lesson->id_mapel,
            'jam_ke' => 2,
        ]);
    }

    public function test_updating_active_shift_duration_resynchronizes_all_schedule_consumers(): void
    {
        $this->travelTo(Carbon::parse('2026-09-21 07:05:00', 'Asia/Jakarta'));
        [$admin, $teacher, $lesson] = $this->createMondaySchedule();

        $this->actingAs($admin)->post(route('dashboard.jadwal.shift-time'), [
            'hari' => 'Senin',
            'mode' => 'maju',
            'minutes' => 40,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.pengaturan.update'), [
                'action_type' => 'durasi_shift',
                'shift_senin_minutes' => 30,
                'shift_jumat_minutes' => 30,
            ])
            ->assertRedirect(route('admin.pengaturan'));

        $lesson->refresh();

        $this->assertSame('07:10:00', $lesson->jam_mulai);
        $this->assertSame(30, Pengaturan::getValue('senin_shifted_minutes'));
        $this->assertSame(['start' => '07:10', 'end' => '07:50'], app(ScheduleTimeService::class)->slot('Senin', 2));

        $this->actingAs($teacher)
            ->get(route('guru'))
            ->assertSee('07:10 - 07:50');
    }

    public function test_non_admin_cannot_change_school_wide_advanced_schedule(): void
    {
        [, $teacher] = $this->createMondaySchedule();

        $this->actingAs($teacher)
            ->post(route('dashboard.jadwal.shift-time'), [
                'hari' => 'Senin',
                'mode' => 'maju',
                'minutes' => 40,
            ])
            ->assertForbidden();

        $this->assertSame(0, Pengaturan::getValue('senin_is_maju'));
    }

    /**
     * @return array{0: User, 1: User, 2: JadwalPelajaran}
     */
    private function createMondaySchedule(): array
    {
        Pengaturan::setValue('senin_is_maju', 0);
        Pengaturan::setValue('senin_shifted_minutes', 0);
        Pengaturan::setValue('shift_senin_minutes', 40);
        Pengaturan::setValue('tenggat_opsi', 'terbatas_jam');

        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'guru']);
        $class = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 0]);
        $subject = Mapel::create(['kode_mapel' => 'MAT-01', 'nama_mapel' => 'Matematika', 'kategori' => 'umum']);

        $lesson = JadwalPelajaran::create([
            'id_user' => $teacher->id,
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $subject->id,
            'hari' => 'Senin',
            'jam_ke' => 2,
            'jam_mulai' => '07:40:00',
            'jam_selesai' => '08:20:00',
            'mapel' => $subject->nama_mapel,
        ]);

        JadwalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $subject->id,
            'hari' => 'Senin',
            'jam_mulai' => 2,
            'jam_selesai' => 2,
        ]);

        return [$admin, $teacher, $lesson];
    }
}
