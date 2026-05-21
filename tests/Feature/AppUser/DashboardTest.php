<?php

namespace Tests\Feature\AppUser;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
  use RefreshDatabase;

  public function test_dashboard_page_is_displayed_for_authenticated_user(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('appuser.dashboard'));

    $response->assertStatus(200);
  }

  public function test_unauthenticated_user_cannot_access_dashboard(): void
  {
    $response = $this->get(route('appuser.dashboard'));

    $response->assertRedirect(route('login'));
  }
}
