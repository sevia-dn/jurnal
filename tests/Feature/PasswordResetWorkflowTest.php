<?php

namespace Tests\Feature;

use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_with_admin_whatsapp_info(): void
    {
        $adminNumber = app(WhatsAppService::class)->getAdminNumber();
        $cleanNumber = preg_replace('/[^0-9]/', '', $adminNumber);
        $intlNumber = str_starts_with($cleanNumber, '0') ? '62'.substr($cleanNumber, 1) : $cleanNumber;

        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Lupa Password ?');
        $response->assertSee($adminNumber);
        $response->assertSee($intlNumber);
    }

    public function test_user_can_submit_password_reset_request_and_get_redirected_to_admin_whatsapp(): void
    {
        $guru = User::create([
            'name' => 'Guru Pengajar Test',
            'username' => 'gurutest',
            'nip' => '198501012010011001',
            'password' => Hash::make('oldpassword'),
            'role' => 'guru',
            'no_hp' => '081234567890',
        ]);

        $response = $this->post(route('password.request.submit'), [
            'identity' => '198501012010011001',
            'no_hp' => '081234567890',
            'alasan' => 'Lupa password setelah restart HP',
        ]);

        // Harus tercatat di database password_reset_requests
        $this->assertDatabaseHas('password_reset_requests', [
            'user_id' => $guru->id,
            'username' => 'gurutest',
            'nama' => 'Guru Pengajar Test',
            'no_hp' => '081234567890',
            'status' => 'menunggu',
            'alasan' => 'Lupa password setelah restart HP',
        ]);

        $adminNumber = app(WhatsAppService::class)->getAdminNumber();
        $cleanNumber = preg_replace('/[^0-9]/', '', $adminNumber);
        $intlNumber = str_starts_with($cleanNumber, '0') ? '62'.substr($cleanNumber, 1) : $cleanNumber;

        // Harus redirect ke tautan wa.me Admin
        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('https://wa.me/'.$intlNumber, $targetUrl);
        $this->assertStringContainsString('PERMOHONAN+RESET+PASSWORD', $targetUrl);
        $this->assertStringContainsString('Guru+Pengajar+Test', $targetUrl);
    }

    public function test_user_can_submit_password_reset_request_via_ajax_and_receive_json(): void
    {
        $user = User::create([
            'name' => 'Staff Pengurus',
            'username' => 'staffpengurus',
            'password' => Hash::make('secret'),
            'role' => 'pengurus_kelas',
        ]);

        $response = $this->postJson(route('password.request.submit'), [
            'identity' => 'staffpengurus',
            'no_hp' => '08987654321',
        ]);

        $adminNumber = app(WhatsAppService::class)->getAdminNumber();
        $cleanNumber = preg_replace('/[^0-9]/', '', $adminNumber);
        $intlNumber = str_starts_with($cleanNumber, '0') ? '62'.substr($cleanNumber, 1) : $cleanNumber;

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $data = $response->json();
        $this->assertArrayHasKey('wa_url', $data);
        $this->assertStringContainsString('https://wa.me/'.$intlNumber, $data['wa_url']);
    }

    public function test_admin_can_approve_password_reset_and_generate_message_template(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Ahmad Guru',
            'username' => 'ahmadguru',
            'password' => Hash::make('password_lama'),
            'role' => 'guru',
            'no_hp' => '081299998888',
        ]);

        $laporan = PasswordResetRequest::create([
            'user_id' => $user->id,
            'nama' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'no_hp' => $user->no_hp,
            'alasan' => 'Lupa kata sandi',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->post("/dashboard/admin/laporan-ganti-pw/{$laporan->id}/terima", [
            'password_baru' => 'GantiPassword2026!',
            'catatan' => 'Sudah diverifikasi via telepon',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('pesan_wa_reset');
        $response->assertSessionHas('wa_send_url');

        // Pastikan password user di DB sudah terupdate
        $user->refresh();
        $this->assertTrue(Hash::check('GantiPassword2026!', $user->password));

        // Pastikan status laporan sudah disetujui
        $laporan->refresh();
        $this->assertSame('disetujui', $laporan->status);
        $this->assertSame($admin->id, $laporan->handled_by);

        // Pastikan template pesan WhatsApp berisi username dan password baru
        $template = session('pesan_wa_reset');
        $this->assertStringContainsString('ahmadguru', $template);
        $this->assertStringContainsString('GantiPassword2026!', $template);
        $this->assertStringContainsString('INFORMASI RESET PASSWORD', $template);

        // Pastikan wa_send_url mengarah ke nomor user
        $waUrl = session('wa_send_url');
        $this->assertStringContainsString('https://wa.me/6281299998888', $waUrl);
    }

    public function test_admin_can_reject_password_reset_request(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin_test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $laporan = PasswordResetRequest::create([
            'nama' => 'Orang Asing',
            'username' => 'orangasing',
            'role' => 'guru',
            'alasan' => 'Akun bukan miliknya',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->post("/dashboard/admin/laporan-ganti-pw/{$laporan->id}/tolak", [
            'catatan' => 'Data tidak cocok dengan arsip sekolah.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $laporan->refresh();
        $this->assertSame('ditolak', $laporan->status);
    }
}
