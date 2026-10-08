<?php

namespace Tests\Feature;

use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengurusKelasNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_clicking_a_journal_notification_marks_it_read_and_opens_the_journal(): void
    {
        [$pengurus, $jurnal] = $this->createJournalForClassManager();
        $notification = Notifikasi::create([
            'id_user' => $pengurus->id,
            'id_kelas' => $jurnal->id_kelas,
            'id_jurnal' => $jurnal->id_jurnal,
            'judul' => 'Jurnal Baru Menunggu Validasi',
            'pesan' => 'Silakan periksa jurnal.',
            'tipe' => 'jurnal_baru',
            'is_read' => false,
        ]);

        $this->actingAs($pengurus)
            ->post(route('pengurus-kelas.notifikasi.read', $notification))
            ->assertRedirect(route('pengurus-kelas.jurnal-detail', ['id' => $jurnal->id_jurnal]));

        $this->assertDatabaseHas('notifikasis', [
            'id' => $notification->id,
            'is_read' => true,
        ]);

    }

    public function test_validating_a_journal_marks_its_notification_read(): void
    {
        [$pengurus, $jurnal] = $this->createJournalForClassManager();
        $notification = Notifikasi::create([
            'id_user' => $pengurus->id,
            'id_kelas' => $jurnal->id_kelas,
            'id_jurnal' => $jurnal->id_jurnal,
            'judul' => 'Jurnal Baru Menunggu Validasi',
            'pesan' => 'Silakan periksa jurnal.',
            'tipe' => 'jurnal_baru',
            'is_read' => false,
        ]);

        $this->actingAs($pengurus)
            ->post(route('pengurus-kelas.jurnal-validasi', ['id' => $jurnal->id_jurnal]), ['action' => 'setujui'])
            ->assertRedirect(route('pengurus-kelas.jurnal-detail', ['id' => $jurnal->id_jurnal]));

        $this->assertDatabaseHas('notifikasis', [
            'id' => $notification->id,
            'is_read' => true,
        ]);

        $this->assertDatabaseHas('notifikasis', [
            'id_user' => $jurnal->id_user,
            'id_jurnal' => null,
            'judul' => 'Logbook Sudah Tervalidasi',
            'pesan' => 'Logbook sudah tervalidasi oleh Pengurus Kelas.',
            'tipe' => 'logbook_disetujui',
        ]);
    }

    /**
     * @return array{0: User, 1: JurnalMengajar}
     */
    private function createJournalForClassManager(): array
    {
        $kelas = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 0]);
        $pengurus = User::factory()->create([
            'role' => 'pengurus_kelas',
            'name' => 'Pengurus Kelas X RPL 1',
        ]);
        $guru = User::factory()->create(['role' => 'guru']);
        $mapel = Mapel::create(['kode_mapel' => 'INF', 'nama_mapel' => 'Informatika']);
        $jurnal = JurnalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => now()->toDateString(),
            'jam_ke' => 1,
            'materi' => 'Algoritma',
            'status_validasi' => 'belum_divalidasi',
        ]);

        return [$pengurus, $jurnal];
    }
}
