<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Dispensasi;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DispensasiDualRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispensasi_flow_from_piket_to_waka_approval()
    {
        // 1. Buat User Guru Piket & User Waka
        $guruPiket = User::create([
            'name' => 'Guru Piket Test',
            'username' => 'gurupiket',
            'nip' => '1111222233334444',
            'no_hp' => '081211112222',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_waka' => false
        ]);

        $waka = User::create([
            'name' => 'Waka Test',
            'username' => 'wakatest',
            'nip' => '5555666677778888',
            'no_hp' => '081233334444',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'is_waka' => true
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
            'jk' => 'L',
        ]);

        // 2. Guru Piket Login & Input Dispensasi Siswa
        $response = $this->actingAs($guruPiket)->post(route('piket.dispensasi.store'), [
            'siswa_id' => $siswa->id,
            'jenis_dispensasi' => 'Lomba O2SN',
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

        // Logout guru piket untuk menguji kondisi Guest
        auth()->logout();

        // 3. Uji Waka Klik Link WA saat Belum Login (Guest)
        $approvalUrl = route('dispensasi.approval', ['token' => $dispensasi->token_approval]);
        
        $guestResponse = $this->get($approvalUrl);
        // Harus di-redirect ke login
        $guestResponse->assertRedirect(route('login'));

        // 4. Waka Login -> Harus Otomatis Redirect ke Intended URL (Halaman Approval)
        $loginResponse = $this->post(route('login'), [
            'identity' => 'wakatest',
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect($approvalUrl);

        // 5. Waka Akses Halaman Approval
        $approvalPageResponse = $this->actingAs($waka)->get($approvalUrl);
        $approvalPageResponse->assertStatus(200);
        $approvalPageResponse->assertSee('Budi Pertiwi');
        $approvalPageResponse->assertSee('Lomba O2SN');

        // 6. Waka Klik "Setujui Dispensasi"
        $processResponse = $this->actingAs($waka)->post(route('dispensasi.process', $dispensasi->id), [
            'keputusan' => 'disetujui',
            'catatan_waka' => 'Disetujui. Harap menjaga nama baik sekolah.',
        ]);

        $processResponse->assertRedirect(route('guru'));

        // Pastikan DB ter-update
        $dispensasi->refresh();
        $this->assertEquals('disetujui', $dispensasi->status_waka);
        $this->assertEquals('disetujui', $dispensasi->status_akhir);
        $this->assertEquals($waka->id, $dispensasi->diproses_oleh);
    }
}
