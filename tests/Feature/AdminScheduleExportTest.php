<?php

namespace Tests\Feature;

use App\Models\JadwalPiket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScheduleExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_penugasan_piket_menampilkan_data_per_tanggal_dan_shift(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru', 'name' => 'Guru Piket Pagi']);
        $waka = User::factory()->create(['role' => 'guru', 'name' => 'Waka Piket']);

        JadwalPiket::create([
            'user_id' => $guru->id,
            'hari' => 'Senin',
            'tanggal' => '2026-09-07 00:00:00',
            'tipe' => 'guru',
            'shift' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '11:00:00',
        ]);
        JadwalPiket::create([
            'user_id' => $waka->id,
            'hari' => 'Senin',
            'tanggal' => '2026-09-07 00:00:00',
            'tipe' => 'waka',
            'shift' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '15:00:00',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.jadwal-penugasan', ['bulan' => 9, 'tahun' => 2026]));

        $response->assertSee('Senin, 07 September 2026')
            ->assertSee('Guru Piket Pagi')
            ->assertSee('Waka Piket');
    }

    public function test_unduhan_rekap_jurnal_mengembalikan_berkas_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.rekap-jurnal.download-pdf', ['tanggal' => '2026-09-07']));

        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
    }

    public function test_pengaturan_mingguan_menyimpan_tiga_guru_koordinator_dan_waka_per_sesi_tanpa_menimpa_penugasan_bertanggal(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $petugasTanggal = User::factory()->create(['role' => 'guru']);
        $petugasMingguan = User::factory()->count(9)->create(['role' => 'guru']);

        JadwalPiket::create([
            'user_id' => $petugasTanggal->id,
            'hari' => 'Senin',
            'tanggal' => '2026-09-07 00:00:00',
            'tipe' => 'guru',
            'shift' => 1,
            'jam_mulai' => '07:00:00',
            'jam_selesai' => '11:00:00',
        ]);

        $this->actingAs($admin)->post(route('dashboard.rekap-jurnal.penugasan-piket'), [
            'penugasan' => [
                'Senin' => [
                    'pagi' => [
                        'guru' => [$petugasMingguan[0]->id, $petugasMingguan[1]->id, $petugasMingguan[2]->id],
                        'koordinator' => $petugasMingguan[3]->id,
                    ],
                    'siang' => [
                        'guru' => [$petugasMingguan[4]->id, $petugasMingguan[5]->id, $petugasMingguan[6]->id],
                        'koordinator' => $petugasMingguan[7]->id,
                    ],
                    'waka' => $petugasMingguan[8]->id,
                ],
            ],
        ])->assertRedirectToRoute('dashboard.jadwal-penugasan');

        $this->assertDatabaseHas('jadwal_pikets', [
            'user_id' => $petugasTanggal->id,
            'tanggal' => '2026-09-07 00:00:00',
            'tipe' => 'guru',
        ]);
        $this->assertSame(9, JadwalPiket::query()->whereNull('tanggal')->where('hari', 'Senin')->count());
        $this->assertDatabaseHas('jadwal_pikets', ['user_id' => $petugasMingguan[0]->id, 'tanggal' => null, 'hari' => 'Senin', 'tipe' => 'guru', 'shift' => 1]);
        $this->assertDatabaseHas('jadwal_pikets', ['user_id' => $petugasMingguan[2]->id, 'tanggal' => null, 'hari' => 'Senin', 'tipe' => 'guru', 'shift' => 1]);
        $this->assertDatabaseHas('jadwal_pikets', ['user_id' => $petugasMingguan[3]->id, 'tanggal' => null, 'hari' => 'Senin', 'tipe' => 'koordinator', 'shift' => 1]);
        $this->assertDatabaseHas('jadwal_pikets', ['user_id' => $petugasMingguan[4]->id, 'tanggal' => null, 'hari' => 'Senin', 'tipe' => 'guru', 'shift' => 2]);
        $this->assertDatabaseHas('jadwal_pikets', ['user_id' => $petugasMingguan[6]->id, 'tanggal' => null, 'hari' => 'Senin', 'tipe' => 'guru', 'shift' => 2]);
        $this->assertDatabaseHas('jadwal_pikets', ['user_id' => $petugasMingguan[7]->id, 'tanggal' => null, 'hari' => 'Senin', 'tipe' => 'koordinator', 'shift' => 2]);
        $this->assertDatabaseHas('jadwal_pikets', ['user_id' => $petugasMingguan[8]->id, 'tanggal' => null, 'hari' => 'Senin', 'tipe' => 'waka', 'shift' => 1]);
    }
}
