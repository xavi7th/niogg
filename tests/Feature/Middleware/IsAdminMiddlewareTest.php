<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IsAdminMiddlewareTest extends TestCase
{
  use RefreshDatabase;

  public function test_admin_can_access_admin_routes(): void
  {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
  }

  public function test_super_admin_can_access_admin_routes(): void
  {
    $superAdmin = User::factory()->superAdmin()->create();

    $response = $this->actingAs($superAdmin)->get('/admin/dashboard');

    $response->assertStatus(200);
  }

  public function test_regular_user_cannot_access_admin_routes(): void
  {
    $user = User::factory()->create(['is_admin' => FALSE, 'is_super_admin' => FALSE]);

    $response = $this->actingAs($user)->get('/admin/dashboard');

    $response->assertStatus(403);
  }

  public function test_unauthenticated_user_cannot_access_admin_routes(): void
  {
    $response = $this->get('/admin/dashboard');

    $response->assertRedirect('/login');
  }
}
