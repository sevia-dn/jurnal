<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_read_own_notification_and_is_redirected_to_history(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $notification = Notifikasi::create([
            'id_user' => $teacher->id,
            'judul' => 'Logbook Disetujui',
            'pesan' => 'Logbook Anda telah disetujui.',
            'tipe' => 'logbook_disetujui',
            'is_read' => false,
        ]);

        $this->actingAs($teacher)
            ->post(route('guru.notifikasi.read', $notification))
            ->assertRedirect(route('guru.riwayat'));

        $this->assertDatabaseHas('notifikasis', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }

    public function test_teacher_cannot_read_another_teachers_notification(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $otherTeacher = User::factory()->create(['role' => 'guru']);
        $notification = Notifikasi::create([
            'id_user' => $otherTeacher->id,
            'judul' => 'Privat',
            'pesan' => 'Notifikasi guru lain.',
            'tipe' => 'logbook_disetujui',
            'is_read' => false,
        ]);

        $this->actingAs($teacher)
            ->post(route('guru.notifikasi.read', $notification))
            ->assertNotFound();

        $this->assertDatabaseHas('notifikasis', [
            'id' => $notification->id,
            'is_read' => false,
        ]);
    }

    public function test_teacher_can_mark_all_of_their_notifications_as_read(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        $otherTeacher = User::factory()->create(['role' => 'guru']);

        $firstNotification = Notifikasi::create([
            'id_user' => $teacher->id,
            'judul' => 'Logbook Disetujui',
            'pesan' => 'Logbook pertama telah disetujui.',
            'tipe' => 'logbook_disetujui',
            'is_read' => false,
        ]);
        $secondNotification = Notifikasi::create([
            'id_user' => $teacher->id,
            'judul' => 'Logbook Direvisi',
            'pesan' => 'Logbook kedua perlu diperbaiki.',
            'tipe' => 'logbook_revisi',
            'is_read' => false,
        ]);
        $otherNotification = Notifikasi::create([
            'id_user' => $otherTeacher->id,
            'judul' => 'Privat',
            'pesan' => 'Notifikasi guru lain.',
            'tipe' => 'logbook_disetujui',
            'is_read' => false,
        ]);

        $this->actingAs($teacher)
            ->from(route('guru.utama'))
            ->post(route('guru.notifikasi.read-all'))
            ->assertRedirect(route('guru.utama'));

        $this->assertDatabaseHas('notifikasis', [
            'id' => $firstNotification->id,
            'is_read' => true,
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'id' => $secondNotification->id,
            'is_read' => true,
        ]);
        $this->assertDatabaseHas('notifikasis', [
            'id' => $otherNotification->id,
            'is_read' => false,
        ]);
    }
}
