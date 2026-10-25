<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_events(): void
    {
        $response = $this->get('/admin/acara');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_user_is_forbidden_from_admin_events(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin/acara');

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_events(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/acara');

        $response->assertOk();
    }

    public function test_admin_can_log_in_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin-test@example.com',
            'password' => 'correct-password',
            'role' => 'admin',
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => 'admin-test@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_login_rejects_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'admin-test@example.com',
            'password' => 'correct-password',
            'role' => 'admin',
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => 'admin-test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_non_admin_cannot_log_in_to_admin_panel(): void
    {
        User::factory()->create([
            'email' => 'participant-test@example.com',
            'password' => 'correct-password',
            'role' => 'participant',
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => 'participant-test@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_log_out_and_cannot_access_dashboard_afterward(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }
}
