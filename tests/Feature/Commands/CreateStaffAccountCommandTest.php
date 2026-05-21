<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\UserAuth\Notifications\StaffAccountCreated;

class CreateStaffAccountCommandTest extends TestCase
{
  use RefreshDatabase;

  public function test_creates_staff_account_with_all_options(): void
  {
    Notification::fake();

    $this->artisan('niogg:create-staff-account', [
      '--name' => 'Test Staff',
      '--email' => 'staff@example.com',
      '--password' => 'secure-password-123',
    ])->expectsConfirmation('Send welcome email?', 'no')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', [
      'email' => 'staff@example.com',
      'name' => 'Test Staff',
      'is_admin' => TRUE,
    ]);

    $user = User::where('email', 'staff@example.com')->first();
    $this->assertNotNull($user->email_verified_at);

    Notification::assertNothingSent();
  }

  public function test_sends_welcome_email_when_send_email_flag_is_passed(): void
  {
    Notification::fake();

    $this->artisan('niogg:create-staff-account', [
      '--name' => 'Notified Staff',
      '--email' => 'notified@example.com',
      '--password' => 'secure-password-123',
      '--send-email' => TRUE,
    ])->assertSuccessful();

    $user = User::where('email', 'notified@example.com')->first();

    Notification::assertSentTo(
        [$user],
        StaffAccountCreated::class
    );
  }

  public function test_fails_on_duplicate_email(): void
  {
    User::factory()->create(['email' => 'existing@example.com']);

    $this->artisan('niogg:create-staff-account', [
      '--name' => 'Duplicate',
      '--email' => 'existing@example.com',
      '--password' => 'secure-password-123',
    ])->assertFailed();
  }

  public function test_fails_on_invalid_email_format(): void
  {
    $this->artisan('niogg:create-staff-account', [
      '--name' => 'Bad Email',
      '--email' => 'not-an-email',
      '--password' => 'secure-password-123',
    ])->assertFailed();
  }

  public function test_generates_random_password_when_not_provided(): void
  {
    Notification::fake();

    $this->artisan('niogg:create-staff-account')
        ->expectsQuestion('Full name', 'Random Staff')
        ->expectsQuestion('Email address', 'random@example.com')
        ->expectsConfirmation('Use a random password?', 'yes')
        ->expectsConfirmation('Send welcome email?', 'no')
        ->assertSuccessful();

    $this->assertDatabaseHas('users', [
      'email' => 'random@example.com',
      'name' => 'Random Staff',
      'is_admin' => TRUE,
    ]);
  }
}
