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
}
