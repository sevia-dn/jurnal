<?php

namespace Tests\Feature;

use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicJournalHistoryFilterTest extends TestCase
{
    use RefreshDatabase;

    protected mixed $originalPublikRiwayatAktif;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalPublikRiwayatAktif = Pengaturan::getValue('publik_riwayat_aktif', 1);
        Pengaturan::setValue('publik_riwayat_aktif', 1);
    }

    protected function tearDown(): void
    {
        Pengaturan::setValue('publik_riwayat_aktif', $this->originalPublikRiwayatAktif);
        parent::tearDown();
    }

    public function test_teacher_can_open_public_journal_history_without_logging_out(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);

        $this->actingAs($teacher)
            ->get(route('guru.riwayat-publik'))
            ->assertOk()
            ->assertSee('Riwayat Jurnal')
            ->assertSee('Jurnal Hari Ini')
            ->assertSee('Riwayat Keseluruhan');
    }

    public function test_public_journal_history_filters_validated_journals_by_range_and_teacher_or_class(): void
    {
        $teacherMatch = User::factory()->create(['name' => 'Siti Rahmawati']);
        $teacherOther = User::factory()->create(['name' => 'Budi Santoso']);
        $classMatch = Kelas::create(['nama_kelas' => 'XI RPL 1', 'jumlah_siswa' => 36]);
        $classOther = Kelas::create(['nama_kelas' => 'X TKJ 1', 'jumlah_siswa' => 36]);
        $mapel = Mapel::create(['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika']);

        JurnalMengajar::create([
            'id_user' => $teacherMatch->id,
            'id_kelas' => $classMatch->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => '2026-09-10',
            'jam_ke' => 1,
            'materi' => 'Materi sesuai filter',
            'status_validasi' => 'disetujui',
        ]);
        JurnalMengajar::create([
            'id_user' => $teacherOther->id,
            'id_kelas' => $classOther->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => '2026-09-20',
            'jam_ke' => 1,
            'materi' => 'Materi di luar tanggal',
            'status_validasi' => 'disetujui',
        ]);
        JurnalMengajar::create([
            'id_user' => $teacherMatch->id,
            'id_kelas' => $classMatch->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => '2026-09-11',
            'jam_ke' => 1,
            'materi' => 'Materi belum disetujui',
            'status_validasi' => 'belum_divalidasi',
        ]);

        $this->get(route('public.jurnal.riwayat', [
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2026-09-15',
            'search' => 'XI RPL',
        ]))
            ->assertOk()
            ->assertSee('Materi sesuai filter')
            ->assertDontSee('Materi di luar tanggal')
            ->assertDontSee('Materi belum disetujui');

        $this->get(route('public.jurnal.riwayat', ['search' => 'Siti Rahmawati']))
            ->assertOk()
            ->assertSee('Materi sesuai filter')
            ->assertDontSee('Materi di luar tanggal');
    }
}
