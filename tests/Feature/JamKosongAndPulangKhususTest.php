<?php

namespace Tests\Feature;

use App\Models\JadwalMengajar;
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

class JamKosongAndPulangKhususTest extends TestCase
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

    public function test_admin_can_configure_jam_kosong_seharian(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.pengaturan.update'), [
            'action_type' => 'jam_kosong',
            'jam_kosong_nama' => 'Classmeet Semester Ganjil',
            'jam_kosong_tanggal_mulai' => '2026-10-01',
            'jam_kosong_tanggal_selesai' => '2026-10-03',
        ]);

        $response->assertRedirect(route('admin.pengaturan').'#pemajuan-jam');
        $this->assertSame('Classmeet Semester Ganjil', Pengaturan::getValue('jam_kosong_nama'));
        $this->assertSame('2026-10-01', Pengaturan::getValue('jam_kosong_tanggal_mulai'));
        $this->assertSame('2026-10-03', Pengaturan::getValue('jam_kosong_tanggal_selesai'));

        $service = app(ScheduleTimeService::class);
        $this->assertTrue($service->isAllDayEmptyForDate('2026-10-01'));
        $this->assertTrue($service->isAllDayEmptyForDate('2026-10-02'));
        $this->assertTrue($service->isAllDayEmptyForDate('2026-10-03'));
        $this->assertFalse($service->isAllDayEmptyForDate('2026-10-04'));
    }

    public function test_admin_can_configure_jam_pulang_khusus(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.pengaturan.update'), [
            'action_type' => 'event',
            'event_sekolah' => 'Rapat Guru & Pulang Cepat',
            'event_sekolah_tanggal' => '2026-10-05',
            'event_sekolah_jam_pulang' => '10:00',
        ]);

        $response->assertRedirect(route('admin.pengaturan').'#pemajuan-jam');
        $this->assertSame('Rapat Guru & Pulang Cepat', Pengaturan::getValue('event_sekolah'));
        $this->assertSame('2026-10-05', Pengaturan::getValue('event_sekolah_tanggal'));
        $this->assertSame('10:00', Pengaturan::getValue('event_sekolah_jam_pulang'));

        $service = app(ScheduleTimeService::class);
        $this->assertSame('10:00', $service->dismissalTimeForDate('2026-10-05'));
        $this->assertNull($service->dismissalTimeForDate('2026-10-06'));
    }

    public function test_jam_kosong_seharian_blocks_guru_journal_submission_and_shows_banner(): void
    {
        $this->travelTo(Carbon::parse('2026-10-02 08:00:00', 'Asia/Jakarta'));

        Pengaturan::setValue('jam_kosong_nama', 'Classmeet Tahunan');
        Pengaturan::setValue('jam_kosong_tanggal_mulai', '2026-10-01');
        Pengaturan::setValue('jam_kosong_tanggal_selesai', '2026-10-03');

        $teacher = User::factory()->create(['role' => 'guru']);
        $class = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 30]);
        $mapel = Mapel::create(['kode_mapel' => 'MAT-01', 'nama_mapel' => 'Matematika', 'kategori' => 'umum']);

        JadwalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $mapel->id,
            'hari' => 'Jumat',
            'jam_mulai' => 1,
            'jam_selesai' => 2,
        ]);

        $response = $this->actingAs($teacher)->get(route('guru'));
        $response->assertOk();
        $response->assertSee('Classmeet Tahunan');
        $response->assertSee('Hari ini tidak ada kegiatan belajar mengajar. Pengisian jurnal tidak diperlukan.');

        Storage::fake('public');
        $submit = $this->actingAs($teacher)->post(route('guru.jurnal.store'), [
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $mapel->id,
            'jam_ke' => 1,
            'jam_selesai' => 2,
            'materi' => 'Mencoba submit jurnal saat classmeet',
            'ada_tugas' => 'Tidak',
            'lampiran' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $submit->assertSessionHas('error');
        $this->assertDatabaseMissing('jurnal_mengajars', [
            'id_user' => $teacher->id,
            'materi' => 'Mencoba submit jurnal saat classmeet',
        ]);
    }

    public function test_jam_pulang_khusus_blocks_sessions_ending_after_dismissal_time(): void
    {
        $this->travelTo(Carbon::parse('2026-10-05 08:00:00', 'Asia/Jakarta'));

        Pengaturan::setValue('event_sekolah', 'Pulang Cepat Dadakan');
        Pengaturan::setValue('event_sekolah_tanggal', '2026-10-05');
        Pengaturan::setValue('event_sekolah_jam_pulang', '10:00');

        $teacher = User::factory()->create(['role' => 'guru']);
        $class = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 30]);
        $mapel = Mapel::create(['kode_mapel' => 'BIN-01', 'nama_mapel' => 'Bahasa Indonesia', 'kategori' => 'umum']);

        // Sesi 1-2: 07:00 - 08:20 (applicable, selesai sebelum 10:00)
        JadwalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $mapel->id,
            'hari' => 'Senin',
            'jam_mulai' => 1,
            'jam_selesai' => 2,
        ]);

        // Sesi 6-7: 10:40 - 12:00 (non-applicable, selesai setelah 10:00)
        JadwalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $mapel->id,
            'hari' => 'Senin',
            'jam_mulai' => 6,
            'jam_selesai' => 7,
        ]);

        $response = $this->actingAs($teacher)->get(route('guru'));
        $response->assertOk();
        $response->assertSee('Pulang Cepat Dadakan hari ini');

        // Sesi 6-7 seharusnya tidak tampil di jadwal beranda guru karena ditiadakan
        $jadwalsInView = $response->viewData('jadwals');
        $this->assertCount(1, $jadwalsInView);
        $this->assertSame(1, $jadwalsInView->first()->jam_mulai);

        Storage::fake('public');
        $submit = $this->actingAs($teacher)->post(route('guru.jurnal.store'), [
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $mapel->id,
            'jam_ke' => 6,
            'jam_selesai' => 7,
            'materi' => 'Submit sesi yang sudah dibatalkan pulang cepat',
            'ada_tugas' => 'Tidak',
            'lampiran' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $submit->assertSessionHas('error');
        $this->assertDatabaseMissing('jurnal_mengajars', [
            'id_user' => $teacher->id,
            'jam_ke' => 6,
        ]);
    }

    public function test_pengurus_kelas_shows_zero_sessions_and_banner_during_jam_kosong(): void
    {
        $this->travelTo(Carbon::parse('2026-10-02 08:00:00', 'Asia/Jakarta'));

        Pengaturan::setValue('jam_kosong_nama', 'Classmeet');
        Pengaturan::setValue('jam_kosong_tanggal_mulai', '2026-10-01');
        Pengaturan::setValue('jam_kosong_tanggal_selesai', '2026-10-03');

        $class = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 30]);
        $pengurus = User::factory()->create([
            'role' => 'pengurus_kelas',
            'name' => 'Pengurus Kelas X RPL 1',
        ]);

        $teacher = User::factory()->create(['role' => 'guru']);
        $mapel = Mapel::create(['kode_mapel' => 'MAT-01', 'nama_mapel' => 'Matematika', 'kategori' => 'umum']);

        JadwalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $mapel->id,
            'hari' => 'Jumat',
            'jam_mulai' => 1,
            'jam_selesai' => 2,
        ]);

        $response = $this->actingAs($pengurus)->get(route('pengurus-kelas.dashboard'));
        $response->assertOk();
        $response->assertSee('Classmeet');
        $response->assertSee('Hari ini tidak ada kegiatan belajar mengajar reguler');
        $this->assertSame(0, $response->viewData('totalSesi'));
    }
}
