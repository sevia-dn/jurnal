<?php

namespace Tests\Feature;

use App\Models\Dispensasi;
use App\Models\JadwalMengajar;
use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\KehadiranGuru;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PengurusKelasFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengurus_kelas_dashboard_loads_without_errors(): void
    {
        $response = $this->get('/');
        Carbon::setLocale('id');
        $now = Carbon::parse('2026-09-24 08:00:00', 'Asia/Jakarta');
        $this->travelTo($now);
        $hariIni = $now->translatedFormat('l');

        $response->assertStatus(200);
        $kelas = Kelas::create([
            'nama_kelas' => 'XI RPL 2',
            'jumlah_siswa' => 36,
        ]);

        $pengurus = User::create([
            'name' => 'Pengurus Kelas XI RPL 2',
            'username' => 'xirpl2',
            'password' => Hash::make('password123'),
            'role' => 'pengurus_kelas',
        ]);

        $guru = User::create([
            'name' => 'Budi Santoso, S.Kom',
            'username' => 'budisantoso',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'nip' => '198001012005011001',
        ]);

        $mapel = Mapel::create([
            'nama_mapel' => 'Pemrograman Web',
            'kode_mapel' => 'PWEB',
        ]);

        $jadwal = JadwalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'hari' => $hariIni,
            'jam_mulai' => 1,
            'jam_selesai' => 3,
        ]);

        $jurnal = JurnalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => $now->toDateString(),
            'jam_ke' => 1,
            'jam_selesai' => 3,
            'materi' => 'Pengenalan Laravel Blade & Routing',
            'status_validasi' => 'belum_divalidasi',
            'jumlah_hadir' => 35,
            'jumlah_sakit' => 1,
            'jumlah_izin' => 0,
            'jumlah_alpa' => 0,
            'jumlah_dispensasi' => 0,
        ]);

        $response = $this->actingAs($pengurus)->get(route('pengurus-kelas.dashboard'));

        $response->assertOk();
        $response->assertSee('XI RPL 2');
        $response->assertDontSee('Kehadiran Guru');
        $response->assertSee('Kelas Hari Ini');
        $response->assertSee('Perlu Persetujuan');
        $response->assertSee('Kehadiran Siswa');
        $response->assertSee('Jadwal Pembelajaran Hari Ini');
        $response->assertSee('Pemrograman Web');
        $response->assertSee('Budi Santoso, S.Kom');
        $response->assertSee('Pengenalan Laravel Blade & Routing');
        $response->assertSee(route('pengurus-kelas.kehadiran-siswa'));
    }

    public function test_pengurus_kelas_jurnal_detail_and_history(): void
    {
        $kelas = Kelas::create([
            'nama_kelas' => 'XI RPL 2',
            'jumlah_siswa' => 36,
        ]);

        $pengurus = User::create([
            'name' => 'Pengurus Kelas XI RPL 2',
            'username' => 'xirpl2',
            'password' => Hash::make('password123'),
            'role' => 'pengurus_kelas',
        ]);

        $guru = User::create([
            'name' => 'Siti Aminah, M.Pd',
            'username' => 'sitiaminah',
            'password' => Hash::make('password123'),
            'role' => 'guru',
        ]);

        $mapel = Mapel::create([
            'nama_mapel' => 'Basis Data',
            'kode_mapel' => 'BDAT',
        ]);

        $jurnal = JurnalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => now()->toDateString(),
            'jam_ke' => 4,
            'jam_selesai' => 5,
            'materi' => 'Query JOIN SQL',
            'status_validasi' => 'belum_divalidasi',
            'jumlah_hadir' => 36,
        ]);

        $resIndex = $this->actingAs($pengurus)->get(route('pengurus-kelas.jurnal-detail'));
        $resIndex->assertOk();
        $resIndex->assertSee('Query JOIN SQL');

        $resDetail = $this->actingAs($pengurus)->get(route('pengurus-kelas.jurnal-detail', ['id' => $jurnal->id_jurnal]));
        $resDetail->assertOk();
        $resDetail->assertSee('Basis Data');
    }

    public function test_pengurus_can_search_and_filter_all_logbook_history_by_date(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'X PPLG 1', 'jumlah_siswa' => 36]);
        $pengurus = User::create([
            'name' => 'Pengurus Kelas X PPLG 1',
            'username' => 'xpplg1',
            'password' => Hash::make('password'),
            'role' => 'pengurus_kelas',
        ]);
        $guruMatematika = User::factory()->create(['name' => 'Guru Matematika', 'role' => 'guru']);
        $guruBahasa = User::factory()->create(['name' => 'Guru Bahasa', 'role' => 'guru']);
        $matematika = Mapel::create(['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK']);
        $bahasa = Mapel::create(['nama_mapel' => 'Bahasa Indonesia', 'kode_mapel' => 'BIND']);

        $validatedJournal = JurnalMengajar::create([
            'id_user' => $guruMatematika->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $matematika->id,
            'tanggal' => '2026-09-20',
            'jam_ke' => 1,
            'materi' => 'Persamaan linear',
            'status_validasi' => 'disetujui',
        ]);
        JurnalMengajar::create([
            'id_user' => $guruBahasa->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $bahasa->id,
            'tanggal' => '2026-09-19',
            'jam_ke' => 2,
            'materi' => 'Menulis teks eksposisi',
            'status_validasi' => 'belum_divalidasi',
        ]);

        $history = $this->actingAs($pengurus)->get(route('pengurus-kelas.jurnal-detail'));
        $history->assertOk()
            ->assertSee('Riwayat Logbook')
            ->assertSee('Guru Matematika')
            ->assertSee('Guru Bahasa')
            ->assertSee('type="date"', false)
            ->assertSee('name="search"', false);

        $this->actingAs($pengurus)
            ->get(route('pengurus-kelas.jurnal-detail', [
                'tanggal_mulai' => '2026-09-19',
                'tanggal_selesai' => '2026-09-19',
                'search' => 'Bahasa',
            ]))
            ->assertOk()
            ->assertSee('Guru Bahasa')
            ->assertDontSee('Guru Matematika');

        $this->actingAs($pengurus)
            ->get(route('pengurus-kelas.jurnal-detail', ['id' => $validatedJournal->id_jurnal]))
            ->assertOk()
            ->assertDontSee('Setujui Logbook')
            ->assertSee('tindakan validasi tidak tersedia lagi.');

        $this->actingAs($pengurus)
            ->post(route('pengurus-kelas.jurnal-validasi', ['id' => $validatedJournal->id_jurnal]), ['action' => 'setujui'])
            ->assertSessionHas('error');

        $this->get('/pengurus-kelas/jadwal')->assertNotFound();
    }

    public function test_dispensasi_aktif_overrides_kehadiran_siswa_status(): void
    {
        Carbon::setLocale('id');
        $now = Carbon::parse('2026-09-24 08:00:00', 'Asia/Jakarta');
        $this->travelTo($now);

        $kelas = Kelas::create(['nama_kelas' => 'XI RPL 1', 'jumlah_siswa' => 30]);
        $pengurus = User::create([
            'name' => 'Pengurus Kelas XI RPL 1',
            'username' => 'xirpl1',
            'password' => Hash::make('password'),
            'role' => 'pengurus_kelas',
        ]);
        $siswa = Siswa::create([
            'nama' => 'Ayu Lestari',
            'nis' => '12345',
            'kelas_id' => $kelas->id_kelas,
            'jenis_kelamin' => 'P',
        ]);
        $piket = User::create([
            'name' => 'Piket Harian',
            'username' => 'piket1',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Buat dispensasi aktif hari ini dan disetujui
        Dispensasi::create([
            'siswa_id' => $siswa->id,
            'jenis_dispensasi' => 'sekolah',
            'tipe_dispensasi' => 'satu_hari',
            'tanggal' => $now->toDateString(),
            'tanggal_selesai' => $now->toDateString(),
            'alasan' => 'Lomba OSN',
            'status_akhir' => 'disetujui',
            'dibuat_oleh' => $piket->id,
        ]);

        $response = $this->actingAs($pengurus)->get(route('pengurus-kelas.kehadiran-siswa'));
        $response->assertOk();
        $response->assertSee('Dispensasi');
        $response->assertSee('Ayu Lestari');
    }

    public function test_piket_store_kehadiran_guru_creates_notification_for_pengurus_kelas(): void
    {
        Carbon::setLocale('id');
        $now = Carbon::parse('2026-09-24 08:00:00', 'Asia/Jakarta');
        $this->travelTo($now);
        $hariIni = $now->translatedFormat('l');

        $kelas = Kelas::create(['nama_kelas' => 'XI RPL 3', 'jumlah_siswa' => 32]);
        $pengurus = User::create([
            'name' => 'Pengurus Kelas XI RPL 3',
            'username' => 'xirpl3',
            'password' => Hash::make('password'),
            'role' => 'pengurus_kelas',
        ]);
        $guru = User::create([
            'name' => 'Siti Rahayu, M.Pd',
            'username' => 'sitirahayu',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);
        $mapel = Mapel::create(['nama_mapel' => 'Matematika', 'kode_mapel' => 'MTK']);
        $piketUser = User::create([
            'name' => 'Petugas Piket',
            'username' => 'piketuser',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Jadwal piket untuk piketUser hari ini
        JadwalPiket::create([
            'user_id' => $piketUser->id,
            'hari' => $hariIni,
            'tanggal' => $now->toDateString(),
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '14:00:00',
        ]);

        // Absen piket hari ini
        KehadiranGuru::create([
            'user_id' => $piketUser->id,
            'tanggal' => $now->toDateString(),
            'status' => 'Hadir',
            'jam_masuk' => '07:00:00',
        ]);

        // Jadwal mengajar guru di kelas ini hari ini
        JadwalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'hari' => $hariIni,
            'jam_mulai' => 1,
            'jam_selesai' => 3,
        ]);

        $response = $this->actingAs($piketUser)->post(route('piket.kehadiran.store'), [
            'user_id' => $guru->id,
            'status' => 'Sakit',
            'keterangan' => 'Demam tinggi',
        ]);

        $response->assertRedirect(route('piket.kehadiran'));

        $this->assertDatabaseHas('notifikasis', [
            'id_user' => $pengurus->id,
            'id_kelas' => $kelas->id_kelas,
            'tipe' => 'guru_tidak_hadir',
            'is_read' => false,
        ]);
    }
}
