<?php

namespace Tests\Feature\UserAuth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
  use RefreshDatabase;

  public function test_login_screen_can_be_rendered(): void
  {
    $response = $this->get(route('login'));
    $response->assertStatus(200);
  }

  public function test_users_can_authenticate_using_the_login_screen(): void
  {
    $user = User::factory()->create();

    $response = $this->post(route('login'), [
      'email' => $user->email,
      'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('appuser.dashboard', absolute: FALSE));
  }

  public function test_users_can_not_authenticate_with_invalid_password(): void
  {
    $user = User::factory()->create();

    $this->post(route('login'), [
      'email' => $user->email,
      'password' => 'wrong-password',
    ]);

    $this->assertGuest();
  }

  public function test_users_can_logout(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $this->assertGuest();
    $response->assertRedirect('/');
  }

  public function test_login_redirects_admins_to_admin_dashboard(): void
  {
    $admin = User::factory()->admin()->create();

    $response = $this->post(route('login'), [
      'email' => $admin->email,
      'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: FALSE));
  }
}
