<?php

namespace Tests\Unit\UserAuth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Notification;
use Modules\UserAuth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\UserAuth\Listeners\UserEventSubscriber;
use Modules\UserAuth\Notifications\SendPasswordResetSuccessfulNotification;

class UserEventSubscriberTest extends TestCase
{
  use RefreshDatabase;

  public function test_registered_event_triggers_verification_notification(): void
  {
    Notification::fake();
    Event::fake();

    $user = User::factory()->unverified()->create();

    event(new Registered($user));

    Notification::assertSentTo($user, VerifyEmail::class);
  }

  public function test_password_reset_event_triggers_successful_notification(): void
  {
    Notification::fake();

    $user = User::factory()->create();

    event(new PasswordReset($user));

    Notification::assertSentTo($user, SendPasswordResetSuccessfulNotification::class);
  }

  public function test_subscriber_registers_correct_events(): void
  {
    $subscriber = new UserEventSubscriber();

    $subscribedEvents = $subscriber->subscribe([]);

    $this->assertArrayHasKey(Registered::class, $subscribedEvents);
    $this->assertArrayHasKey(PasswordReset::class, $subscribedEvents);
  }
}
