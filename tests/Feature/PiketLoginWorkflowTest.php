<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Dispensasi;
use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PiketLoginWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduled_teacher_lands_on_guru_dashboard_and_can_access_piket(): void
    {
        $this->travelTo(Carbon::parse('2026-09-22 08:00:00', 'Asia/Jakarta'));
        $guru = $this->teacher('petugas-piket');
        $this->schedule($guru, '2026-09-22');

        $this->post('/login', ['identity' => $guru->username, 'password' => 'password'])
            ->assertRedirect(route('guru'));

        $this->actingAs($guru)
            ->get(route('dashboard.piket'))
            ->assertOk();
    }

    public function test_unscheduled_teacher_lands_on_teaching_dashboard(): void
    {
        $this->travelTo(Carbon::parse('2026-09-22 08:00:00', 'Asia/Jakarta'));
        $guru = $this->teacher('guru-pengajar');

        $this->post('/login', ['identity' => $guru->username, 'password' => 'password'])
            ->assertRedirect(route('guru'));

        $this->actingAs($guru)
            ->get(route('dashboard.piket'))
            ->assertSee('Anda Tidak Sedang Piket');
    }

    public function test_pengurus_kelas_can_log_in_and_open_its_dashboard(): void
    {
        $pengurus = User::factory()->create([
            'name' => 'Pengurus Kelas X RPL 1',
            'username' => 'pengurus-xrpl1',
            'role' => 'pengurus_kelas',
            'password' => Hash::make('password'),
        ]);
        Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 0]);

        $this->post('/login', ['identity' => $pengurus->username, 'password' => 'password'])
            ->assertRedirect(route('pengurus-kelas.dashboard'));

        $this->actingAs($pengurus)
            ->get(route('pengurus-kelas.dashboard'))
            ->assertOk()
            ->assertSee('Pengurus Harian Kelas X RPL 1');
    }

    public function test_waka_on_piket_schedule_lands_on_piket_and_keeps_approval_permission(): void
    {
        $this->travelTo(Carbon::parse('2026-09-22 08:00:00', 'Asia/Jakarta'));
        $waka = $this->teacher('waka-piket', true);
        $this->schedule($waka, '2026-09-22', 'waka');
        $dispensasi = $this->dispensasi();

        $this->post('/login', ['identity' => $waka->username, 'password' => 'password'])
            ->assertRedirect(route('guru'));

        $this->actingAs($waka)
            ->get(route('dashboard.piket'))
            ->assertOk();

        $this->actingAs($waka)
            ->post(route('dispensasi.process', $dispensasi), ['keputusan' => 'disetujui'])
            ->assertRedirect(route('dispensasi.approval', ['token' => $dispensasi->token_approval]));

        $this->assertDatabaseHas('dispensasis', [
            'id' => $dispensasi->id,
            'status_akhir' => 'disetujui',
            'diproses_oleh' => $waka->id,
        ]);
    }

    public function test_non_waka_cannot_validate_a_dispensasi(): void
    {
        $guru = $this->teacher('bukan-waka');
        $dispensasi = $this->dispensasi();

        $this->actingAs($guru)
            ->post(route('dispensasi.process', $dispensasi), ['keputusan' => 'disetujui'])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('dispensasis', [
            'id' => $dispensasi->id,
            'status_akhir' => 'menunggu',
        ]);
    }

    public function test_unscheduled_teacher_accessing_piket_sees_not_scheduled_view(): void
    {
        $this->travelTo(Carbon::parse('2026-09-22 08:00:00', 'Asia/Jakarta'));
        $guru = $this->teacher('guru-tidak-piket');

        $response = $this->actingAs($guru)->get(route('dashboard.piket'));

        $response->assertOk();
        $response->assertSee('Anda Tidak Sedang Piket');
        $response->assertSee('Halaman Utama Guru');
        $response->assertSee('Anda sedang tidak piket');
        $response->assertDontSee('Detail Logbook Mengajar');
    }

    public function test_scheduled_teacher_can_access_all_piket_modules(): void
    {
        $this->travelTo(Carbon::parse('2026-09-22 08:00:00', 'Asia/Jakarta'));
        $guru = $this->teacher('guru-piket-lengkap');
        $this->schedule($guru, '2026-09-22');

        $this->actingAs($guru)->get(route('dashboard.piket'))->assertOk()->assertSee('Pengajuan Dispensasi');
        $this->actingAs($guru)->get(route('piket.kehadiran'))->assertOk()->assertSee('Daftar Kehadiran Guru');
        $this->actingAs($guru)->get(route('piket.kehadiran.form'))->assertOk()->assertSee('Lapor Kehadiran Guru');
        $this->actingAs($guru)->get(route('piket.dispensasi.form'))->assertOk()->assertSee('Form Pengajuan Dispensasi');
    }

    public function test_guru_navigation_includes_piket_menu(): void
    {
        $guru = $this->teacher('guru-navigasi');

        $response = $this->actingAs($guru)->get(route('guru.utama'));

        $response->assertOk();
        $response->assertSee('Anda sedang tidak piket');
    }

    public function test_piket_dashboard_uses_journal_data_and_only_records_sick_or_permission_reports(): void
    {
        $this->travelTo(Carbon::parse('2026-09-22 08:00:00', 'Asia/Jakarta'));
        $petugas = $this->teacher('petugas-monitoring');
        $guruHadir = $this->teacher('guru-jurnal');
        $guruSakit = $this->teacher('guru-sakit');
        $this->schedule($petugas, '2026-09-22');

        $kelas = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 0]);
        $mapel = Mapel::create(['kode_mapel' => 'INF', 'nama_mapel' => 'Informatika']);
        $jurnal = JurnalMengajar::create([
            'id_user' => $guruHadir->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => '2026-09-22',
            'jam_ke' => 1,
            'materi' => 'Algoritma dasar',
            'status_validasi' => 'disetujui',
        ]);

        $this->actingAs($petugas)
            ->get(route('dashboard.piket'))
            ->assertOk()
            ->assertSee('Guru jurnal')
            ->assertSee('Algoritma dasar')
            ->assertSee('Sudah divalidasi')
            ->assertSee(route('piket.jurnal.show', $jurnal));

        $this->actingAs($petugas)
            ->get(route('piket.jurnal.show', $jurnal))
            ->assertOk()
            ->assertSee('Detail logbook mengajar')
            ->assertSee('Algoritma dasar')
            ->assertSee('Absensi Siswa');

        $this->actingAs($petugas)
            ->post(route('piket.kehadiran.store'), [
                'user_id' => $guruSakit->id,
                'status' => 'Sakit',
                'keterangan' => 'Istirahat karena sakit.',
            ])
            ->assertRedirect(route('piket.kehadiran'));

        $this->assertDatabaseHas('kehadiran_gurus', [
            'user_id' => $guruSakit->id,
            'tanggal' => '2026-09-22',
            'status' => 'Sakit',
            'keterangan' => 'Istirahat karena sakit.',
        ]);

        $this->actingAs($petugas)
            ->post(route('piket.kehadiran.store'), [
                'user_id' => $guruHadir->id,
                'status' => 'Izin',
                'keterangan' => 'Tidak boleh menimpa jurnal.',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('kehadiran_gurus', [
            'user_id' => $guruHadir->id,
            'tanggal' => '2026-09-22',
        ]);
    }

    public function test_piket_can_only_change_student_attendance_for_today(): void
    {
        $this->travelTo(Carbon::parse('2026-09-22 08:00:00', 'Asia/Jakarta'));
        $petugas = $this->teacher('petugas-presensi-siswa');
        $this->schedule($petugas, '2026-09-22');
        $kelas = Kelas::create(['nama_kelas' => 'X RPL 3', 'jumlah_siswa' => 1]);
        $siswa = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '2001', 'nama' => 'Siswa Presensi', 'jenis_kelamin' => 'L']);

        $this->actingAs($petugas)
            ->post(route('piket.kehadiran-siswa.update'), [
                'siswa_id' => $siswa->id,
                'kelas_id' => $kelas->id_kelas,
                'tanggal' => '2026-09-21',
                'status' => 'Izin',
                'catatan' => 'Surat izin.',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing(PiketKehadiranSiswa::class, [
            'siswa_id' => $siswa->id,
            'tanggal' => '2026-09-21',
        ]);

        $this->actingAs($petugas)
            ->post(route('piket.kehadiran-siswa.update'), [
                'siswa_id' => $siswa->id,
                'kelas_id' => $kelas->id_kelas,
                'tanggal' => '2026-09-22',
                'status' => 'Izin',
                'catatan' => 'Surat izin.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas(PiketKehadiranSiswa::class, [
            'siswa_id' => $siswa->id,
            'status' => 'Izin',
            'catatan' => 'Surat izin.',
        ]);
        $this->assertTrue(PiketKehadiranSiswa::query()
            ->where('siswa_id', $siswa->id)
            ->whereDate('tanggal', '2026-09-22')
            ->exists());
    }

    public function test_piket_can_save_changed_student_attendance_with_one_bulk_submission(): void
    {
        $this->travelTo(Carbon::parse('2026-09-22 08:00:00', 'Asia/Jakarta'));
        $petugas = $this->teacher('petugas-presensi-bulk');
        $this->schedule($petugas, '2026-09-22');
        $kelas = Kelas::create(['nama_kelas' => 'X RPL 4', 'jumlah_siswa' => 2]);
        $siswaSakit = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '3001', 'nama' => 'Siswa Sakit', 'jenis_kelamin' => 'L']);
        $siswaIzin = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '3002', 'nama' => 'Siswa Izin', 'jenis_kelamin' => 'P']);

        $this->actingAs($petugas)
            ->post(route('piket.kehadiran-siswa.update'), [
                'bulk_attendance' => true,
                'kelas_id' => $kelas->id_kelas,
                'tanggal' => '2026-09-22',
                'absensi' => [
                    $siswaSakit->id => 'Sakit',
                    $siswaIzin->id => 'Izin',
                ],
                'absensi_catatan' => [
                    $siswaSakit->id => 'Surat dokter.',
                    $siswaIzin->id => 'Izin keluarga.',
                ],
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas(PiketKehadiranSiswa::class, [
            'siswa_id' => $siswaSakit->id,
            'status' => 'Sakit',
            'catatan' => 'Surat dokter.',
        ]);
        $this->assertDatabaseHas(PiketKehadiranSiswa::class, [
            'siswa_id' => $siswaIzin->id,
            'status' => 'Izin',
            'catatan' => 'Izin keluarga.',
        ]);
        $this->assertSame(2, PiketKehadiranSiswa::query()
            ->whereIn('siswa_id', [$siswaSakit->id, $siswaIzin->id])
            ->whereDate('tanggal', '2026-09-22')
            ->count());
    }

    public function test_absensi_sync_between_guru_piket_and_pengurus_kelas(): void
    {
        $this->travelTo(Carbon::parse('2026-09-22 08:00:00', 'Asia/Jakarta'));
        $guru = $this->teacher('guru-pengajar-absensi');
        $petugasPiket = $this->teacher('petugas-piket-absensi');
        $this->schedule($petugasPiket, '2026-09-22');

        $kelas = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 3]);
        $pengurus = User::create([
            'name' => 'Pengurus Kelas X RPL 1',
            'username' => 'xrpl1',
            'password' => Hash::make('xrpl1'),
            'role' => 'pengurus_kelas',
        ]);

        $siswaA = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '1001', 'nama' => 'Ahmad Sakit', 'jenis_kelamin' => 'L']);
        $siswaB = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '1002', 'nama' => 'Budi Izin', 'jenis_kelamin' => 'L']);
        $siswaC = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '1003', 'nama' => 'Citra Hadir', 'jenis_kelamin' => 'P']);

        $mapel = Mapel::create(['kode_mapel' => 'PROG', 'nama_mapel' => 'Pemrograman']);

        // Simulasi input form guru: Siswa A Sakit (ada alasan), Siswa B Izin (ada alasan), Siswa C Hadir
        $jurnal = JurnalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => '2026-09-22',
            'jam_ke' => 1,
            'materi' => 'PHP Dasar',
            'jumlah_hadir' => 1,
            'jumlah_sakit' => 1,
            'jumlah_izin' => 1,
            'jumlah_alpa' => 0,
            'jumlah_dispensasi' => 0,
            'jumlah_tidak_hadir' => 2,
            'status_validasi' => 'belum_divalidasi',
        ]);

        Absensi::create(['id_jurnal' => $jurnal->id_jurnal, 'id_siswa' => $siswaA->id, 'status' => 'Sakit', 'catatan' => 'Demam tinggi']);
        Absensi::create(['id_jurnal' => $jurnal->id_jurnal, 'id_siswa' => $siswaB->id, 'status' => 'Izin', 'catatan' => 'Acara keluarga']);
        Absensi::create(['id_jurnal' => $jurnal->id_jurnal, 'id_siswa' => $siswaC->id, 'status' => 'Hadir', 'catatan' => null]);

        // 1. Verifikasi tampilan Piket Jurnal Detail
        $this->actingAs($petugasPiket)
            ->get(route('piket.jurnal.show', $jurnal))
            ->assertOk()
            ->assertSee('Ahmad Sakit')
            ->assertSee('Demam tinggi')
            ->assertSee('Budi Izin')
            ->assertSee('Acara keluarga')
            ->assertDontSee('Citra Hadir')
            ->assertDontSee('Nihil. Semua siswa tercatat hadir pada jurnal ini.');

        // 2. Verifikasi tampilan Pengurus Kelas Jurnal Detail
        $this->actingAs($pengurus)
            ->get(route('pengurus-kelas.jurnal-detail', ['id' => $jurnal->id_jurnal]))
            ->assertOk()
            ->assertSee('Ahmad Sakit')
            ->assertSee('Demam tinggi')
            ->assertSee('Budi Izin')
            ->assertSee('Acara keluarga')
            ->assertDontSee('Citra Hadir')
            ->assertDontSee('Semua siswa hadir di kelas (Nihil tidak hadir).');

        // 3. Pengurus melihat status yang sama dalam bentuk radio baca-saja.
        $this->actingAs($pengurus)
            ->get(route('pengurus-kelas.kehadiran-siswa'))
            ->assertOk()
            ->assertSee('type="radio"', false)
            ->assertSee('Ahmad Sakit')
            ->assertSee('Demam tinggi')
            ->assertSee('Budi Izin')
            ->assertSee('Acara keluarga');
    }

    private function teacher(string $username, bool $isWaka = false): User
    {
        return User::factory()->create([
            'name' => ucfirst(str_replace('-', ' ', $username)),
            'username' => $username,
            'role' => 'guru',
            'is_waka' => $isWaka,
            'password' => Hash::make('password'),
        ]);
    }

    private function schedule(User $user, string $date, string $type = 'guru'): void
    {
        JadwalPiket::create([
            'user_id' => $user->id,
            'hari' => Carbon::parse($date)->locale('id')->translatedFormat('l'),
            'tanggal' => $date,
            'tipe' => $type,
            'shift' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '15:00:00',
        ]);
    }

    private function dispensasi(): Dispensasi
    {
        $kelas = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 1]);
        $siswa = Siswa::create([
            'kelas_id' => $kelas->id_kelas,
            'nis' => '2026001',
            'nama' => 'Siswa Uji',
            'jenis_kelamin' => 'L',
        ]);

        return Dispensasi::create([
            'siswa_id' => $siswa->id,
            'jenis_dispensasi' => 'Kegiatan sekolah',
            'tipe_dispensasi' => 'satu_hari',
            'tanggal' => '2026-09-22',
            'tanggal_selesai' => '2026-09-22',
            'alasan' => 'Pengujian otorisasi',
            'status_piket' => 'disetujui',
            'status_waka' => 'menunggu',
            'status_akhir' => 'menunggu',
            'token_approval' => 'approval-token-'.uniqid(),
        ]);
    }
}
