<?php

namespace Tests\Feature\Console\Commands;

use App\Models\JadwalMengajar;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncAdminSchedulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_synchronizes_class_x_and_xi_schedules_without_touching_other_grades(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $subject = Mapel::create(['kode_mapel' => 'MAT-01', 'nama_mapel' => 'Matematika', 'kategori' => 'biasa']);
        $classX = Kelas::create(['nama_kelas' => 'X RPL 1', 'jumlah_siswa' => 0]);
        $classXi = Kelas::create(['nama_kelas' => 'XI RPL 1', 'jumlah_siswa' => 0]);
        $classXii = Kelas::create(['nama_kelas' => 'XII RPL 1', 'jumlah_siswa' => 0]);

        JadwalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $classX->id_kelas,
            'id_mapel' => $subject->id,
            'hari' => 'Senin',
            'jam_mulai' => 2,
            'jam_selesai' => 3,
        ]);
        JadwalMengajar::create([
            'id_user' => $teacher->id,
            'id_kelas' => $classXi->id_kelas,
            'id_mapel' => $subject->id,
            'hari' => 'Jumat',
            'jam_mulai' => 9,
            'jam_selesai' => 10,
        ]);
        $staleAdminSchedule = JadwalPelajaran::create([
            'id_user' => $teacher->id,
            'id_kelas' => $classX->id_kelas,
            'id_mapel' => $subject->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:40:00',
            'mapel' => 'Jadwal Lama',
        ]);
        JadwalPelajaran::create([
            'id_user' => $teacher->id,
            'id_kelas' => $classXii->id_kelas,
            'id_mapel' => $subject->id,
            'hari' => 'Senin',
            'jam_ke' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:40:00',
            'mapel' => 'Jadwal Kelas XII',
        ]);

        $this->artisan('jadwal:sync-admin')
            ->expectsOutput('30 jadwal kelas X dan XI berhasil diselaraskan ke Admin.')
            ->assertSuccessful();

        $this->assertDatabaseMissing('jadwal_pelajarans', ['id_jadwal' => $staleAdminSchedule->id_jadwal]);
        $this->assertDatabaseHas('jadwal_pelajarans', [
            'id_kelas' => $classX->id_kelas,
            'id_mapel' => $subject->id,
            'hari' => 'Senin',
            'jam_ke' => 2,
            'jam_mulai' => '07:40:00',
            'jam_selesai' => '09:00:00',
            'mapel' => 'Matematika',
        ]);
        $this->assertDatabaseHas('jadwal_pelajarans', [
            'id_kelas' => $classXi->id_kelas,
            'hari' => 'Jumat',
            'jam_ke' => 9,
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '14:00:00',
        ]);
        $this->assertDatabaseHas('jadwal_pelajarans', [
            'id_kelas' => $classXii->id_kelas,
            'mapel' => 'Jadwal Kelas XII',
        ]);

        $this->artisan('jadwal:sync-admin')->assertSuccessful();

        $this->assertDatabaseHas('jadwal_pelajarans', [
            'id_kelas' => $classX->id_kelas,
            'hari' => 'Senin',
            'mapel' => 'Upacara / Apel',
            'jam_ke' => 0,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:40:00',
        ]);
        $this->assertDatabaseHas('jadwal_pelajarans', [
            'id_kelas' => $classXi->id_kelas,
            'hari' => 'Jumat',
            'mapel' => 'Pembiasaan Jumat',
            'jam_ke' => 0,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '07:30:00',
        ]);
        $this->assertDatabaseHas('jadwal_pelajarans', [
            'id_kelas' => $classX->id_kelas,
            'hari' => 'Jumat',
            'mapel' => 'Istirahat 2',
            'jam_ke' => 0,
            'jam_mulai' => '11:20:00',
            'jam_selesai' => '13:00:00',
        ]);
        $this->assertSame(30, JadwalPelajaran::query()->whereIn('id_kelas', [$classX->id_kelas, $classXi->id_kelas])->count());
    }
}
