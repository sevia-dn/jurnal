<?php

namespace Tests\Feature;

use App\Models\KetidakhadiranGuru;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WakaTeacherAbsenceConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_absence_submission_creates_notifications_for_both_piket_and_waka(): void
    {
        $date = '2026-10-07';
        $teacher = User::factory()->create(['role' => 'guru']);
        $waka = User::factory()->create(['role' => 'guru', 'is_waka' => true]);

        $this->actingAs($teacher)
            ->post(route('guru.ketidakhadiran.store'), [
                'tanggal' => $date,
                'alasan' => 'izin',
                'keterangan' => 'Menghadiri dinas luar.',
            ])
            ->assertRedirect(route('guru.utama'));

        $absence = KetidakhadiranGuru::where('user_id', $teacher->id)->firstOrFail();
        $this->assertSame('pending', $absence->status_konfirmasi_waka);

        $wakaNotif = Notifikasi::where('id_user', $waka->id)
            ->where('tipe', 'waka_konfirmasi_guru_absen')
            ->first();

        $this->assertNotNull($wakaNotif);
        $this->assertSame($absence->id, $wakaNotif->id_ketidakhadiran_guru);
    }

    public function test_waka_can_confirm_teacher_absence(): void
    {
        $date = '2026-10-07';
        $teacher = User::factory()->create(['role' => 'guru']);
        $waka = User::factory()->create(['role' => 'guru', 'is_waka' => true]);

        $absence = KetidakhadiranGuru::create([
            'user_id' => $teacher->id,
            'tanggal' => $date,
            'alasan' => 'sakit',
            'keterangan' => 'Demam tinggi.',
            'status' => 'pending',
            'status_konfirmasi_waka' => 'pending',
        ]);

        $this->actingAs($waka)
            ->post(route('waka.ketidakhadiran.konfirmasi', $absence), [
                'keputusan' => 'dikonfirmasi',
                'catatan_waka' => 'Semoga lekas sembuh.',
            ])
            ->assertRedirect(route('guru.utama'));

        $absence->refresh();
        $this->assertSame('dikonfirmasi', $absence->status_konfirmasi_waka);
        $this->assertSame($waka->id, $absence->dikonfirmasi_oleh_waka);
        $this->assertSame('Semoga lekas sembuh.', $absence->catatan_waka);

        $teacherNotif = Notifikasi::where('id_user', $teacher->id)
            ->where('tipe', 'konfirmasi_waka_hasil')
            ->first();

        $this->assertNotNull($teacherNotif);
        $this->assertStringContainsString('dikonfirmasi', $teacherNotif->pesan);
    }

    public function test_waka_can_reject_teacher_absence(): void
    {
        $date = '2026-10-07';
        $teacher = User::factory()->create(['role' => 'guru']);
        $waka = User::factory()->create(['role' => 'guru', 'is_waka' => true]);

        $absence = KetidakhadiranGuru::create([
            'user_id' => $teacher->id,
            'tanggal' => $date,
            'alasan' => 'izin',
            'keterangan' => 'Acara mendadak.',
            'status' => 'pending',
            'status_konfirmasi_waka' => 'pending',
        ]);

        $this->actingAs($waka)
            ->post(route('waka.ketidakhadiran.konfirmasi', $absence), [
                'keputusan' => 'ditolak',
                'catatan_waka' => 'Tidak dapat diizinkan karena jadwal ujian.',
            ])
            ->assertRedirect(route('guru.utama'));

        $absence->refresh();
        $this->assertSame('ditolak', $absence->status_konfirmasi_waka);
        $this->assertSame($waka->id, $absence->dikonfirmasi_oleh_waka);
    }

    public function test_non_waka_cannot_confirm_teacher_absence(): void
    {
        $date = '2026-10-07';
        $teacher = User::factory()->create(['role' => 'guru', 'is_waka' => false]);
        $otherTeacher = User::factory()->create(['role' => 'guru', 'is_waka' => false]);

        $absence = KetidakhadiranGuru::create([
            'user_id' => $teacher->id,
            'tanggal' => $date,
            'alasan' => 'izin',
            'status' => 'pending',
            'status_konfirmasi_waka' => 'pending',
        ]);

        $this->actingAs($otherTeacher)
            ->post(route('waka.ketidakhadiran.konfirmasi', $absence), [
                'keputusan' => 'dikonfirmasi',
            ])
            ->assertForbidden();
    }
}
