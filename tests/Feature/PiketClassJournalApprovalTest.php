<?php

namespace Tests\Feature;

use App\Models\JadwalPelajaran;
use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PiketClassJournalApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_piket_can_only_approve_a_class_after_every_session_is_validated(): void
    {
        $date = '2026-09-22';
        $this->travelTo(Carbon::parse("{$date} 08:00:00", 'Asia/Jakarta'));

        $piket = User::factory()->create(['role' => 'guru']);
        $teacher = User::factory()->create(['role' => 'guru']);
        $class = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 0]);
        $subject = Mapel::create(['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika']);
        JadwalPiket::create([
            'user_id' => $piket->id,
            'tanggal' => $date,
            'hari' => 'Selasa',
            'tipe' => 'guru',
            'shift' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '12:00:00',
        ]);
        JadwalPelajaran::create([
            'id_user' => $teacher->id,
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $subject->id,
            'hari' => 'Selasa',
            'jam_ke' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:40:00',
            'mapel' => $subject->nama_mapel,
            'status' => 'aktif',
        ]);
        $journal = JurnalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $class->id_kelas,
            'id_mapel' => $subject->id,
            'tanggal' => $date,
            'jam_ke' => 1,
            'materi' => 'Bilangan',
            'status_validasi' => 'belum_divalidasi',
        ]);

        $this->actingAs($piket)
            ->get(route('dashboard.piket'))
            ->assertOk()
            ->assertSee('Kelas X RPL 1')
            ->assertSee('Jurnal terisi')
            ->assertSee('1/1')
            ->assertSee('Menunggu validasi pengurus');

        $this->actingAs($piket)
            ->get(route('piket.rekap-jurnal'))
            ->assertOk()
            ->assertSee('Riwayat &amp; Rekap Jurnal', false);

        $this->actingAs($piket)
            ->get(route('piket.jurnal-kelas.sessions', ['kelas' => $class, 'tanggal' => $date]))
            ->assertOk()
            ->assertSee('Sesi pembelajaran')
            ->assertSee('Matematika')
            ->assertSee('Menunggu validasi pengurus');

        $this->actingAs($piket)
            ->post(route('piket.rekap-jurnal.kelas.approve', $class), ['tanggal' => $date])
            ->assertSessionHas('error');
        $this->assertDatabaseMissing('persetujuan_jurnal_kelas', ['kelas_id' => $class->id_kelas, 'tanggal' => $date]);

        $journal->update(['status_validasi' => 'disetujui']);

        $this->actingAs($piket)
            ->get(route('dashboard.piket'))
            ->assertOk()
            ->assertSee('Periksa Jurnal');

        $this->actingAs($piket)
            ->get(route('piket.jurnal-kelas.sessions', ['kelas' => $class, 'tanggal' => $date]))
            ->assertOk()
            ->assertSee('Setujui Jurnal Kelas')
            ->assertSee('Lihat Detail Jurnal');

        $this->actingAs($piket)
            ->post(route('piket.rekap-jurnal.kelas.approve', $class), ['tanggal' => $date])
            ->assertSessionHas('success');
        $this->assertDatabaseHas('persetujuan_jurnal_kelas', [
            'kelas_id' => $class->id_kelas,
            'disetujui_oleh' => $piket->id,
        ]);
    }
}
