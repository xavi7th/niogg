<?php

namespace Tests\Unit\UserAuth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\UserAuth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\UserAuth\Notifications\SendPasswordResetNotification;
use Modules\UserAuth\Notifications\SendPasswordResetSuccessfulNotification;

class NotificationsTest extends TestCase
{
  use RefreshDatabase;

  public function test_user_receives_password_reset_notification(): void
  {
    Notification::fake();

    $user = User::factory()->create();
    $user->sendPasswordResetNotification('test-token');

    Notification::assertSentTo($user, SendPasswordResetNotification::class, function ($notification) {
      $this->assertEquals('test-token', $notification->token);

      return TRUE;
    });
  }

  public function test_user_receives_password_reset_successful_notification(): void
  {
    Notification::fake();

    $user = User::factory()->create();
    $user->sendPasswordResetSuccessfulNotification();

    Notification::assertSentTo($user, SendPasswordResetSuccessfulNotification::class);
  }

  public function test_unverified_user_receives_verify_email_notification(): void
  {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class);
  }

  public function test_verify_email_notification_contains_correct_subject(): void
  {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class, function ($notification) use ($user) {
      $mailMessage = $notification->toMail($user);
      $this->assertStringContainsString('Verify Email Address', $mailMessage->subject);

      return TRUE;
    });
  }
}
