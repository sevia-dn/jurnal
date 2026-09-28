<?php

namespace Tests\Feature;

use App\Models\JadwalMengajar;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliKelasJournalTest extends TestCase
{
    use RefreshDatabase;

    public function test_wali_kelas_can_only_see_validated_journals_for_assigned_class(): void
    {
        Carbon::setLocale('id');
        $date = Carbon::parse('2026-09-28 08:00:00', 'Asia/Jakarta');
        $this->travelTo($date);

        $waliKelas = User::factory()->create([
            'name' => 'Siti Wali Kelas, S.Pd.',
            'role' => 'guru',
        ]);
        $guru = User::factory()->create(['name' => 'Guru Pengampu', 'role' => 'guru']);
        $kelas = Kelas::create([
            'nama_kelas' => 'XI RPL 1',
            'wali_kelas' => 'Siti Wali Kelas S Pd',
            'jumlah_siswa' => 36,
        ]);
        $kelasLain = Kelas::create([
            'nama_kelas' => 'XI RPL 2',
            'wali_kelas' => 'Guru Lain',
            'jumlah_siswa' => 36,
        ]);
        $mapel = Mapel::create(['nama_mapel' => 'Pemrograman Web', 'kode_mapel' => 'PWEB']);

        JadwalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'hari' => $date->translatedFormat('l'),
            'jam_mulai' => 1,
            'jam_selesai' => 2,
        ]);

        JurnalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => $date->toDateString(),
            'jam_ke' => 1,
            'jam_selesai' => 2,
            'materi' => 'Materi tervalidasi',
            'status_validasi' => 'disetujui',
            'jumlah_hadir' => 35,
        ]);
        JurnalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => $date->toDateString(),
            'jam_ke' => 3,
            'materi' => 'Materi belum tervalidasi',
            'status_validasi' => 'belum_divalidasi',
        ]);
        JurnalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelasLain->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => $date->toDateString(),
            'jam_ke' => 1,
            'materi' => 'Materi kelas lain',
            'status_validasi' => 'disetujui',
        ]);

        $this->actingAs($waliKelas)
            ->get(route('guru.wali-kelas', ['tanggal' => $date->toDateString()]))
            ->assertOk()
            ->assertSee('XI RPL 1')
            ->assertSee('Materi tervalidasi')
            ->assertDontSee('Materi belum tervalidasi')
            ->assertDontSee('Materi kelas lain')
            ->assertSee('Tervalidasi');
    }

    public function test_teacher_without_wali_kelas_assignment_cannot_open_wali_kelas_page(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);

        $this->actingAs($teacher)
            ->get(route('guru.wali-kelas'))
            ->assertNotFound();
    }
}
