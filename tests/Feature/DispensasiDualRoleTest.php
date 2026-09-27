<?php

namespace Tests\Feature;

use App\Models\Dispensasi;
use App\Models\JadwalPiket;
use App\Models\JurnalMengajar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DispensasiDualRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispensasi_flow_from_piket_to_waka_approval()
    {
        config([
            'services.whatsapp.url' => 'https://gateway.test/send',
            'services.whatsapp.api_key' => 'test-api-key',
            'services.whatsapp.piket_confirmation_number' => '083838606396',
            'services.whatsapp.waka_recipients' => [
                ['username' => 'wakatest', 'name' => 'Waka Test', 'number' => '081233334444'],
            ],
        ]);
        Http::fake();

        // 1. Buat User Guru Piket & User Waka
        $guruPiket = User::create([
            'name' => 'Guru Piket Test',
            'username' => 'gurupiket',
            'nip' => '1111222233334444',
            'no_hp' => '081211112222',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_waka' => false,
        ]);

        $waka = User::create([
            'name' => 'Waka Test',
            'username' => 'wakatest',
            'nip' => '5555666677778888',
            'no_hp' => '081233334444',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_waka' => true,
        ]);

        JadwalPiket::create([
            'user_id' => $guruPiket->id,
            'hari' => now('Asia/Jakarta')->locale('id')->translatedFormat('l'),
            'tanggal' => now('Asia/Jakarta')->toDateString(),
            'tipe' => 'guru',
            'shift' => 1,
            'jam_mulai' => '00:00:00',
            'jam_selesai' => '23:59:59',
        ]);
        JadwalPiket::create([
            'user_id' => $waka->id,
            'hari' => now('Asia/Jakarta')->locale('id')->translatedFormat('l'),
            'tanggal' => now('Asia/Jakarta')->toDateString(),
            'tipe' => 'waka',
            'shift' => 1,
            'jam_mulai' => '00:00:00',
            'jam_selesai' => '23:59:59',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'XI RPL 2',
            'wali_kelas' => 'Dewi Anjani, S.Pd',
            'jumlah_siswa' => 30,
        ]);

        // Buat Siswa
        $siswa = Siswa::create([
            'kelas_id' => $kelas->id_kelas,
            'nama' => 'Budi Pertiwi',
            'nis' => '12345',
            'nisn' => '1234567890',
            'jenis_kelamin' => 'L',
        ]);

        $mapel = Mapel::create([
            'kode_mapel' => 'RPL',
            'nama_mapel' => 'Pemrograman Web',
        ]);

        $jurnal = JurnalMengajar::create([
            'id_user' => $guruPiket->id,
            'id_kelas' => $kelas->id_kelas,
            'id_mapel' => $mapel->id,
            'tanggal' => now()->toDateString(),
            'jam_ke' => 1,
            'materi' => 'Materi pengujian',
        ]);

        // 2. Guru Piket Login & Input Dispensasi Siswa
        $response = $this->actingAs($guruPiket)->post(route('piket.dispensasi.store'), [
            'siswa_id' => $siswa->id,
            'jenis_dispensasi' => 'Lomba O2SN',
            'mode_waktu' => 'sepanjang_hari',
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(2)->format('Y-m-d'),
            'alasan' => 'Mewakili sekolah dalam ajang Lomba Tingkat Provinsi',
        ]);

        $response->assertRedirect(route('piket.dispensasi.form'));

        // Pastikan record Dispensasi berhasil dibuat di DB
        $dispensasi = Dispensasi::where('siswa_id', $siswa->id)->first();
        $this->assertNotNull($dispensasi);
        $this->assertEquals('menunggu', $dispensasi->status_waka);
        $this->assertNotNull($dispensasi->token_approval);
        Http::assertSent(function (ClientRequest $request) use ($dispensasi): bool {
            return $request->url() === 'https://gateway.test/send'
                && $request['target'] === '081233334444'
                && str_contains($request['message'], route('dispensasi.approval', ['token' => $dispensasi->token_approval]))
                && str_contains($request['message'], 'Guru Piket Test');
        });
        Http::assertSentCount(2);

        // Logout guru piket untuk menguji kondisi Guest
        auth()->logout();

        // 3. Uji Waka Klik Link WA saat Belum Login (Guest).
        // Link harus kembali ke dashboard pribadi dengan popup setelah login.
        $dashboardApprovalUrl = route('guru.utama', ['dispensasi' => $dispensasi->token_approval]);
        $guestResponse = $this->get($dashboardApprovalUrl);
        $guestResponse->assertRedirect(route('login'));

        // 4. Waka login lalu diarahkan kembali ke popup di dashboard-nya.
        $loginResponse = $this->post(route('login'), [
            'identity' => 'wakatest',
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect($dashboardApprovalUrl);

        $dashboardPopupResponse = $this->actingAs($waka)->get($dashboardApprovalUrl);
        $dashboardPopupResponse->assertOk();
        $dashboardPopupResponse->assertSee('Detail Pengajuan Dispensasi');
        $dashboardPopupResponse->assertSee('Guru Piket Test');

        // 5. Halaman approval lama tetap tersedia untuk tautan yang sudah terlanjur beredar.
        $approvalUrl = route('dispensasi.approval', ['token' => $dispensasi->token_approval]);
        $approvalPageResponse = $this->actingAs($waka)->get($approvalUrl);
        $approvalPageResponse->assertStatus(200);
        $approvalPageResponse->assertSee('Budi Pertiwi');
        $approvalPageResponse->assertSee('Lomba O2SN');
        $approvalPageResponse->assertSee('Guru Piket Test');

        // 6. Waka Klik "Setujui Dispensasi" dari popup dashboard pribadi.
        $processResponse = $this->actingAs($waka)->post(route('dispensasi.process', $dispensasi->id), [
            'keputusan' => 'disetujui',
            'catatan_waka' => 'Disetujui. Harap menjaga nama baik sekolah.',
            'redirect_ke_dashboard' => true,
        ]);

        $processResponse->assertRedirect(route('guru.utama'));

        // Pastikan DB ter-update
        $dispensasi->refresh();
        $this->assertEquals('disetujui', $dispensasi->status_waka);
        $this->assertEquals('disetujui', $dispensasi->status_akhir);
        $this->assertEquals($waka->id, $dispensasi->diproses_oleh);
        $this->assertNotNull($dispensasi->token_verifikasi);
        $this->assertDatabaseHas('absensis', [
            'id_jurnal' => $jurnal->id_jurnal,
            'id_siswa' => $siswa->id,
            'status' => 'D',
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'id_user' => $guruPiket->id,
            'id_dispensasi' => $dispensasi->id,
            'tipe' => 'dispensasi',
        ]);

        $verificationResponse = $this->get(route('dispensasi.verify', $dispensasi->token_verifikasi));
        $verificationResponse->assertOk();
        $verificationResponse->assertSee('Dispensasi Siswa Valid');
        $verificationResponse->assertSee('Budi Pertiwi');
    }
}
