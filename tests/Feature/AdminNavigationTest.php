<?php

namespace Tests\Feature;

use App\Models\JadwalPelajaran;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_pages_and_actions_have_working_routes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach ([
            'dashboard',
            'dashboard.guru',
            'dashboard.kelas',
            'dashboard.siswa',
            'dashboard.mapel',
            'dashboard.jadwal',
            'admin.manajemen-user',
            'dashboard.rekap-jurnal',
            'admin.pengaturan',
        ] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }

        $this->actingAs($admin)
            ->get(route('catatan-jurnal'))
            ->assertRedirect(route('dashboard.rekap-jurnal'));

        $this->assertTrue(route('dashboard.guru.import') !== '');
        $this->assertTrue(route('dashboard.guru.download-template') !== '');
        $this->assertTrue(route('dashboard.jadwal.import') !== '');
        $this->assertTrue(route('dashboard.jadwal.download-template') !== '');
        $this->assertTrue(route('dashboard.rekap-jurnal.penugasan-piket') !== '');
    }

    public function test_admin_can_manage_mapel_schedule_and_review_journals_with_real_related_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $kelas = Kelas::create([
            'nama_kelas' => 'X RPL 1',
            'wali_kelas' => 'Wali Kelas',
            'jumlah_siswa' => 0,
        ]);

        $this->actingAs($admin)
            ->post(route('dashboard.mapel.store'), [
                'kode_mapel' => 'RPL-01',
                'nama_mapel' => 'Pemrograman Web',
                'kategori' => 'jurusan',
                'guru_names' => json_encode([$guru->name]),
            ])
            ->assertRedirect(route('dashboard.mapel'));

        $mapel = Mapel::where('kode_mapel', 'RPL-01')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'id' => $guru->id,
            'mapel_id' => $mapel->id,
        ]);

        $this->actingAs($admin)
            ->put(route('dashboard.mapel.update', $mapel), [
                'kode_mapel' => 'RPL-02',
                'nama_mapel' => 'Pemrograman Web Lanjutan',
                'kategori' => 'jurusan',
                'guru_names' => json_encode([$guru->name]),
            ])
            ->assertRedirect(route('dashboard.mapel'));

        $mapel->refresh();

        $this->actingAs($admin)
            ->post(route('dashboard.jadwal.store'), [
                'id_user' => $guru->id,
                'kelas_id' => $kelas->id_kelas,
                'mapel_id' => $mapel->id,
                'hari' => 'Senin',
                'jam_ke' => 1,
                'jam_mulai' => '07:00',
                'jam_selesai' => '07:40',
            ])
            ->assertRedirect(route('dashboard.jadwal', ['kelas_id' => $kelas->id_kelas, 'hari' => 'Senin']));

        $jadwal = JadwalPelajaran::firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard.jadwal', ['kelas_id' => $kelas->id_kelas]))
            ->assertOk()
            ->assertSee('Pemrograman Web Lanjutan')
            ->assertSee($guru->name)
            ->assertSee($kelas->nama_kelas);

        $this->actingAs($admin)
            ->put(route('dashboard.jadwal.update', $jadwal), [
                'id_user' => $guru->id,
                'kelas_id' => $kelas->id_kelas,
                'mapel_id' => $mapel->id,
                'hari' => 'Selasa',
                'jam_ke' => 2,
                'jam_mulai' => '08:00',
                'jam_selesai' => '08:40',
            ])
            ->assertRedirect(route('dashboard.jadwal', ['kelas_id' => $kelas->id_kelas, 'hari' => 'Selasa']));

        $this->assertDatabaseHas('jadwal_pelajarans', [
            'id_jadwal' => $jadwal->id_jadwal,
            'hari' => 'Selasa',
            'jam_ke' => 2,
            'mapel' => 'Pemrograman Web Lanjutan',
        ]);

        $jurnal = JurnalMengajar::create([
            'id_user' => $guru->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => now()->toDateString(),
            'jam_ke' => 2,
            'materi' => 'Materi pengujian jurnal',
            'keterangan' => 'Jurnal dapat ditinjau admin.',
        ]);

        $this->actingAs($admin)
            ->followingRedirects()
            ->get(route('catatan-jurnal'))
            ->assertOk()
            ->assertSee($jurnal->materi)
            ->assertSee($guru->name)
            ->assertSee($kelas->nama_kelas);

        $this->actingAs($admin)
            ->delete(route('dashboard.jadwal.destroy', $jadwal))
            ->assertRedirect(route('dashboard.jadwal'));

        $this->assertDatabaseMissing('jadwal_pelajarans', ['id_jadwal' => $jadwal->id_jadwal]);
    }
}
