<?php

namespace Tests\Unit\UserAuth;

use Tests\TestCase;
use App\Models\User;
use Modules\UserAuth\Http\Requests\LoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginRequestTest extends TestCase
{
  use RefreshDatabase;

  public function test_authorize_returns_true_for_any_user(): void
  {
    $user = User::factory()->create();
    $request = new LoginRequest();
    $request->setUserResolver(fn () => $user);

    $this->assertTrue($request->authorize());
  }

  public function test_validation_requires_email_and_password(): void
  {
    $response = $this->post(route('login'), [
      'email' => '',
      'password' => '',
    ]);

    $response->assertSessionHasErrors(['email', 'password']);
  }

  public function test_validation_requires_valid_email_format(): void
  {
    $response = $this->post(route('login'), [
      'email' => 'not-an-email',
      'password' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
  }

  public function test_rate_limiting_blocks_excessive_attempts(): void
  {
    $user = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
      $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
      ]);
    }

    $response = $this->post(route('login'), [
      'email' => $user->email,
      'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
  }
}
