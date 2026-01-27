<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminRouteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_redirects_unauthenticated_user(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admin_dashboard_forbids_non_admin_user(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
            'is_super_admin' => FALSE,
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_dashboard_allows_admin_user(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
            'is_super_admin' => FALSE,
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_admin_dashboard_allows_super_admin_user(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
            'is_super_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_admin_events_index_forbids_non_admin_user(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
        ]);

        $response = $this->actingAs($user)->get('/admin/events');

        $response->assertStatus(403);
    }

    public function test_admin_events_index_allows_admin_user(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->get('/admin/events');

        $response->assertStatus(200);
    }

    public function test_admin_events_create_forbids_non_admin_user(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
        ]);

        $response = $this->actingAs($user)->get('/admin/events/create');

        $response->assertStatus(403);
    }

    public function test_admin_events_create_allows_admin_user(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->get('/admin/events/create');

        $response->assertStatus(200);
    }

    public function test_named_routes_exist(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.events.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('admin.events.create'))->assertStatus(200);
    }
}
