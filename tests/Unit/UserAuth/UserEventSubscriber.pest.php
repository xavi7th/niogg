<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Notification;
use Modules\UserAuth\Notifications\VerifyEmail;
use Modules\UserAuth\Listeners\UserEventSubscriber;
use Modules\UserAuth\Notifications\SendPasswordResetSuccessfulNotification;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('unverified user receives verification notification via subscriber', function (): void {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('password reset event triggers successful notification', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    UserEventSubscriber::onPasswordReset(new PasswordReset($user));

    Notification::assertSentTo($user, SendPasswordResetSuccessfulNotification::class);
});

test('subscriber registers correct events', function (): void {
    $subscriber = new UserEventSubscriber();

    $subscribedEvents = $subscriber->subscribe();

    expect($subscribedEvents)->toHaveKey(PasswordReset::class);
});
