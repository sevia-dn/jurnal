<?php

namespace Tests\Feature;

use App\Models\Pengaturan;
use App\Models\User;
use App\Services\LogbookDeadlinePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogbookDeadlineSettingTest extends TestCase
{
    use RefreshDatabase;

    private string $settingsPath;

    private bool $hadSettingsFile;

    private string|false $originalSettings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settingsPath = storage_path('app/pengaturan.json');
        $this->hadSettingsFile = file_exists($this->settingsPath);
        $this->originalSettings = $this->hadSettingsFile ? file_get_contents($this->settingsPath) : false;
    }

    protected function tearDown(): void
    {
        if ($this->hadSettingsFile) {
            file_put_contents($this->settingsPath, $this->originalSettings);
        } elseif (file_exists($this->settingsPath)) {
            unlink($this->settingsPath);
        }

        parent::tearDown();
    }

    public function test_admin_saved_flexible_policy_is_used_by_teacher_logbook_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'guru']);
        Pengaturan::setValue('tenggat_status', 0);

        $this->actingAs($admin)
            ->post(route('admin.pengaturan.update'), [
                'action_type' => 'tenggat',
                'tenggat_opsi' => 'hari_ini',
            ])
            ->assertRedirect(route('admin.pengaturan'))
            ->assertSessionHas('success');

        $this->assertSame('hari_ini', Pengaturan::getValue('tenggat_opsi'));
        $this->assertSame('hari_ini', app(LogbookDeadlinePolicy::class)->configuration()['mode']);

        $this->actingAs($teacher)
            ->get(route('guru'))
            ->assertSee('Fleksibel jam, tetapi hanya untuk hari ini hingga pukul 23:59 WIB.');
    }

    public function test_non_admin_cannot_change_logbook_deadline_policy(): void
    {
        $teacher = User::factory()->create(['role' => 'guru']);
        Pengaturan::setValue('tenggat_opsi', 'terbatas_jam');

        $this->actingAs($teacher)
            ->post(route('admin.pengaturan.update'), [
                'action_type' => 'tenggat',
                'tenggat_opsi' => 'los',
            ])
            ->assertForbidden();

        $this->assertSame('terbatas_jam', Pengaturan::getValue('tenggat_opsi'));
    }
}
