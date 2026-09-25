<?php

namespace Tests\Unit;

use App\Models\Pengaturan;
use App\Services\LogbookDeadlinePolicy;
use Carbon\Carbon;
use Tests\TestCase;

class LogbookDeadlinePolicyTest extends TestCase
{
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

    public function test_strict_mode_only_allows_an_ongoing_session_today(): void
    {
        $this->setPolicy('terbatas_jam');
        $policy = app(LogbookDeadlinePolicy::class);
        $now = Carbon::parse('2026-09-23 07:20:00', 'Asia/Jakarta');

        $this->assertNull($policy->violation($now, $now, '07:00', '07:40'));
        $this->assertNotNull($policy->violation($now, $now, '08:00', '08:40'));
        $this->assertNotNull($policy->violation($now, $now->copy()->subDay(), '07:00', '07:40'));
    }

    public function test_flexible_daily_mode_allows_any_time_today_only(): void
    {
        $this->setPolicy('hari_ini');
        $policy = app(LogbookDeadlinePolicy::class);
        $now = Carbon::parse('2026-09-23 22:55:00', 'Asia/Jakarta');

        $this->assertNull($policy->violation($now, $now, '07:00', '07:40'));
        $this->assertNotNull($policy->violation($now, $now->copy()->subDay(), '07:00', '07:40'));
        $this->assertNotNull($policy->violation($now, $now->copy()->addDay(), '07:00', '07:40'));
    }

    public function test_los_mode_allows_today_and_yesterday_only(): void
    {
        $now = Carbon::parse('2026-09-23 22:55:00', 'Asia/Jakarta');
        $policy = app(LogbookDeadlinePolicy::class);

        $this->setPolicy('los');
        $this->assertNull($policy->violation($now, $now, '07:00', '07:40'));
        $this->assertNull($policy->violation($now, $now->copy()->subDay(), '07:00', '07:40'));
        $this->assertNotNull($policy->violation($now, $now->copy()->subDays(2), '07:00', '07:40'));
    }

    public function test_selected_policy_is_not_overridden_by_legacy_status_setting(): void
    {
        Pengaturan::setValue('tenggat_status', 0);
        Pengaturan::setValue('tenggat_opsi', 'hari_ini');

        $policy = app(LogbookDeadlinePolicy::class);
        $now = Carbon::parse('2026-09-23 22:55:00', 'Asia/Jakarta');

        $this->assertSame('hari_ini', $policy->configuration()['mode']);
        $this->assertNotNull($policy->violation($now, $now->copy()->subDay(), '07:00', '07:40'));
    }

    private function setPolicy(string $mode): void
    {
        Pengaturan::setValue('tenggat_opsi', $mode);
    }
}
