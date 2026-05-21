<?php

namespace Tests\Feature\AppUser;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
  use RefreshDatabase;

  public function test_profile_page_is_displayed(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('appuser.profile.edit'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('AppUser::Profile/Edit'));
  }

  public function test_profile_information_can_be_updated(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('appuser.profile.update'), [
      'name' => 'Test User',
      'email' => 'test@example.com',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('appuser.profile.edit'));

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
  }

  public function test_email_verification_status_is_unchanged_when_email_is_unchanged(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('appuser.profile.update'), [
      'name' => 'Test User',
      'email' => $user->email,
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertNotNull($user->refresh()->email_verified_at);
  }

  public function test_unauthenticated_user_cannot_access_profile(): void
  {
    $response = $this->get(route('appuser.profile.edit'));

    $response->assertRedirect(route('login'));
  }

  public function test_profile_update_requires_valid_email(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('appuser.profile.update'), [
      'name' => 'Test User',
      'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['email']);
  }

  public function test_profile_update_requires_unique_email(): void
  {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('appuser.profile.update'), [
      'name' => 'Test User',
      'email' => 'existing@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
  }

  public function test_user_can_delete_their_account(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('appuser.profile.destroy'), [
      'password' => 'password',
    ]);

    $response
        ->assertSessionHasNoErrors();

    $this->assertGuest();
    $this->assertNull(User::find($user->id));
  }

  public function test_correct_password_must_be_provided_to_delete_account(): void
  {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('appuser.profile.destroy'), [
      'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['password']);
    $this->assertNotNull(User::find($user->id));
  }
}
