<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Notifikasi;
use App\Models\PiketKehadiranSiswa;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaTerlambatWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_piket_can_record_late_student_and_sends_notification_to_pengurus_kelas(): void
    {
        $date = '2026-09-30';
        $this->travelTo(Carbon::parse("{$date} 07:30:00", 'Asia/Jakarta'));

        $piket = User::factory()->create(['role' => 'guru', 'name' => 'Guru Piket Pak Budi']);
        $kelas = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 1]);
        $siswa = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '1001', 'nama' => 'Ahmad Fajar', 'jenis_kelamin' => 'L']);
        $pengurus = User::factory()->create([
            'role' => 'pengurus_kelas',
            'name' => 'Pengurus Kelas X RPL 1',
        ]);
        $this->schedulePiket($piket, $date);

        $response = $this->actingAs($piket)->post(route('piket.kehadiran-siswa.telat'), [
            'siswa_id' => $siswa->id,
            'alasan' => 'Ban sepeda motor bocor',
            'tindakan' => 'Membersihkan musala 10 menit',
            'tanggal' => $date,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('piket_kehadiran_siswas', [
            'siswa_id' => $siswa->id,
            'status' => 'Terlambat',
            'catatan' => 'Ban sepeda motor bocor (Tindakan Piket: Membersihkan musala 10 menit)',
            'dicatat_oleh' => $piket->id,
        ]);

        $this->assertDatabaseHas('notifikasis', [
            'id_user' => $pengurus->id,
            'id_kelas' => $kelas->id_kelas,
            'tipe' => 'siswa_terlambat',
            'judul' => 'Surat Izin Masuk (Siswa Terlambat)',
        ]);

        $notifikasi = Notifikasi::where('id_user', $pengurus->id)->firstOrFail();
        $this->assertStringContainsString('Ahmad Fajar', $notifikasi->pesan);
        $this->assertStringContainsString('Ban sepeda motor bocor', $notifikasi->pesan);
        $this->assertStringContainsString('surat izin masuk', strtolower($notifikasi->pesan));
    }

    public function test_pengurus_kelas_clicks_notification_and_redirects_to_kehadiran_siswa(): void
    {
        $date = '2026-09-30';
        $this->travelTo(Carbon::parse("{$date} 07:30:00", 'Asia/Jakarta'));

        $piket = User::factory()->create(['role' => 'guru', 'name' => 'Guru Piket Bu Maya']);
        $kelas = Kelas::create(['nama_kelas' => 'XI TKJ 2', 'jumlah_siswa' => 1]);
        $siswa = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '2001', 'nama' => 'Bima Prasetya', 'jenis_kelamin' => 'L']);
        $pengurus = User::factory()->create([
            'role' => 'pengurus_kelas',
            'name' => 'Pengurus Kelas XI TKJ 2',
        ]);
        $this->schedulePiket($piket, $date);

        PiketKehadiranSiswa::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id_kelas,
            'tanggal' => $date,
            'status' => 'Terlambat',
            'sumber' => PiketKehadiranSiswa::SumberGuruPiket,
            'catatan' => 'Terlambat bangun pagi (Tindakan Piket: Menyanyikan lagu Indonesia Raya)',
            'dicatat_oleh' => $piket->id,
        ]);

        $notification = Notifikasi::create([
            'id_user' => $pengurus->id,
            'id_kelas' => $kelas->id_kelas,
            'judul' => 'Surat Izin Masuk (Siswa Terlambat)',
            'pesan' => 'Siswa Bima Prasetya terlambat karena bangun pagi.',
            'tipe' => 'siswa_terlambat',
            'is_read' => false,
        ]);

        $this->actingAs($pengurus)
            ->post(route('pengurus-kelas.notifikasi.read', $notification))
            ->assertRedirect(route('pengurus-kelas.kehadiran-siswa'));

        $this->assertDatabaseHas('notifikasis', [
            'id' => $notification->id,
            'is_read' => true,
        ]);

        $attendanceResponse = $this->actingAs($pengurus)->get(route('pengurus-kelas.kehadiran-siswa'));
        $attendanceResponse->assertOk()
            ->assertSee('Pemberitahuan Siswa Terlambat Hari Ini')
            ->assertSee('Bima Prasetya')
            ->assertSee('Terlambat bangun pagi')
            ->assertSee('Surat Izin')
            ->assertSee('SURAT IZIN MASUK KELAS');

        $dashboardResponse = $this->actingAs($pengurus)->get(route('pengurus-kelas.dashboard'));
        $dashboardResponse->assertOk()
            ->assertSee('Pemberitahuan Siswa Terlambat')
            ->assertSee('Bima Prasetya');
    }

    public function test_piket_page_displays_tardy_summary_and_student_record(): void
    {
        $date = '2026-09-30';
        $this->travelTo(Carbon::parse("{$date} 07:30:00", 'Asia/Jakarta'));

        $piket = User::factory()->create(['role' => 'guru', 'name' => 'Guru Piket Pak Dedi']);
        $kelas = Kelas::create(['nama_kelas' => 'XII RPL 1', 'jumlah_siswa' => 1]);
        $siswa = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '3001', 'nama' => 'Citra Lestari', 'jenis_kelamin' => 'P']);
        $this->schedulePiket($piket, $date);

        PiketKehadiranSiswa::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id_kelas,
            'tanggal' => $date,
            'status' => 'Terlambat',
            'sumber' => PiketKehadiranSiswa::SumberGuruPiket,
            'catatan' => 'Macet parah di jalan raya (Tindakan Piket: Teguran lisan)',
            'dicatat_oleh' => $piket->id,
        ]);

        $response = $this->actingAs($piket)->get(route('piket.kehadiran-siswa', [
            'kelas_id' => $kelas->id_kelas,
            'tanggal' => $date,
        ]));

        $response->assertOk()
            ->assertSee('1 Terlambat')
            ->assertSee('+ Catat Siswa Telat (Izin Masuk)')
            ->assertSee('Catat Siswa Terlambat');
    }

    public function test_tardy_student_synchronization_keeps_student_as_hadir_with_tardy_note(): void
    {
        $date = '2026-09-30';
        $this->travelTo(Carbon::parse("{$date} 07:30:00", 'Asia/Jakarta'));

        $teacher = User::factory()->create(['role' => 'guru', 'name' => 'Guru Pengajar Pak Joko']);
        $piket = User::factory()->create(['role' => 'guru', 'name' => 'Guru Piket Bu Maya']);
        $kelas = Kelas::create(['nama_kelas' => 'X PPLG 1', 'jumlah_siswa' => 1]);
        $subject = Mapel::create(['kode_mapel' => 'DASPROG', 'nama_mapel' => 'Dasar Pemrograman']);
        $siswa = Siswa::create(['kelas_id' => $kelas->id_kelas, 'nis' => '4001', 'nama' => 'Dimas Anggara', 'jenis_kelamin' => 'L']);
        $this->schedulePiket($piket, $date);

        $journal = JurnalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $subject->id,
            'tanggal' => $date,
            'jam_ke' => 1,
            'materi' => 'Pengenalan Variabel',
            'jumlah_hadir' => 1,
            'jumlah_tidak_hadir' => 0,
        ]);
        Absensi::create([
            'id_jurnal' => $journal->id_jurnal,
            'id_siswa' => $siswa->id,
            'status' => 'Hadir',
        ]);

        $this->actingAs($piket)->post(route('piket.kehadiran-siswa.telat'), [
            'siswa_id' => $siswa->id,
            'alasan' => 'Rantai sepeda putus',
            'tindakan' => 'Piket 10 menit',
            'tanggal' => $date,
        ]);

        $this->assertDatabaseHas('absensis', [
            'id_jurnal' => $journal->id_jurnal,
            'id_siswa' => $siswa->id,
            'status' => 'Hadir',
            'catatan' => 'Terlambat: Rantai sepeda putus (Tindakan Piket: Piket 10 menit) (Izin Masuk Piket)',
        ]);

        $this->assertDatabaseHas('jurnal_mengajars', [
            'id_jurnal' => $journal->id_jurnal,
            'jumlah_hadir' => 1,
            'jumlah_tidak_hadir' => 0,
        ]);
    }

    private function schedulePiket(User $piket, string $date): void
    {
        JadwalPiket::create([
            'user_id' => $piket->id,
            'tanggal' => $date,
            'hari' => Carbon::parse($date, 'Asia/Jakarta')->locale('id')->translatedFormat('l'),
            'tipe' => 'guru',
            'shift' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '15:00:00',
        ]);
    }
}
