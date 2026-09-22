<?php

namespace Tests\Feature;

use App\Models\User;
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
}
