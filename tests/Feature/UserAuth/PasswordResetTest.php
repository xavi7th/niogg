<?php

namespace Tests\Feature\UserAuth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
  use RefreshDatabase;

  public function test_reset_password_link_screen_can_be_rendered(): void
  {
    $response = $this->get(route('password.request'));
    $response->assertStatus(200);
  }

  public function test_reset_password_link_can_be_requested(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
  }

  public function test_reset_password_link_can_not_be_requested_for_non_existent_email(): void
  {
    Notification::fake();

    $this->post(route('password.email'), ['email' => 'nonexistent@example.com']);

    Notification::assertNothingSent();
  }

  public function test_reset_password_screen_can_be_rendered_with_valid_token(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->get(route('password.reset', [
        'token' => $notification->token,
        'email' => $user->email,
      ]));

      $response->assertStatus(200);

      return TRUE;
    });
  }

  public function test_password_can_be_reset_with_valid_token(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->post(route('password.store'), [
        'token' => $notification->token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
      ]);

      $response
          ->assertSessionHasNoErrors()
          ->assertRedirect(route('login'));

      return TRUE;
    });
  }

  public function test_password_cannot_be_reset_with_mismatched_confirmation(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
      $response = $this->post(route('password.store'), [
        'token' => $notification->token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'different-password',
      ]);

      $response->assertSessionHasErrors(['password']);

      return TRUE;
    });
  }
}
