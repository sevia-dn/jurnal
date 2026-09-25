<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\GuruSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_log_in_with_username_and_access_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin Sekolah',
            'username' => 'admin1',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('login'), [
            'identity' => 'admin1',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->get(route('dashboard'))->assertOk();
    }

    public function test_admin_cannot_log_in_with_invalid_password(): void
    {
        User::create([
            'name' => 'Admin Sekolah',
            'username' => 'admin1',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'identity' => 'admin1',
                'password' => 'salah',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('identity');

        $this->assertGuest();
    }

    public function test_guru_can_log_in_with_their_own_username_and_password(): void
    {
        $guru = User::create([
            'name' => 'Guru Pengajar',
            'username' => 'gurupengajar',
            'nip' => '19820529 202321 2 015',
            'password' => Hash::make('gurupengajar123'),
            'role' => 'guru',
        ]);

        $this->post(route('login'), [
            'identity' => 'gurupengajar',
            'password' => 'gurupengajar123',
        ])->assertRedirect(route('guru'));

        $this->assertAuthenticatedAs($guru);
    }

    public function test_guru_cannot_log_in_using_another_teachers_default_password(): void
    {
        User::create([
            'name' => 'Guru Pengajar',
            'username' => 'gurupengajar',
            'password' => Hash::make('gurupengajar123'),
            'role' => 'guru',
        ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'identity' => 'gurupengajar',
                'password' => 'guru123',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('identity');

        $this->assertGuest();
    }

    public function test_guru_can_log_in_with_case_insensitive_username_or_nip_without_spaces(): void
    {
        $guru = User::create([
            'name' => 'Winartin, S.Pd',
            'username' => 'winartin',
            'nip' => '19801224 200801 2 016',
            'password' => Hash::make('winartin123'),
            'role' => 'guru',
        ]);

        $this->post(route('login'), [
            'identity' => 'WINARTIN',
            'password' => 'winartin123',
        ])->assertRedirect(route('guru'));

        $this->assertAuthenticatedAs($guru);

        $this->post(route('logout'));

        $this->post(route('login'), [
            'identity' => '198012242008012016',
            'password' => 'winartin123',
        ])->assertRedirect(route('guru'));

        $this->assertAuthenticatedAs($guru);
    }

    public function test_database_seeder_populates_teachers_with_unique_passwords(): void
    {
        $this->seed(GuruSeeder::class);
        $this->seed(UserSeeder::class);

        $this->post(route('login'), [
            'identity' => 'rulydwisetyaningrum',
            'password' => 'ruly123',
        ])->assertRedirect(route('guru'));

        $this->assertAuthenticated();
        $this->post(route('logout'));

        $this->post(route('login'), [
            'identity' => 'trisnowibowo',
            'password' => 'trisno123',
        ])->assertRedirect(route('guru'));

        $this->assertAuthenticated();
    }
}
