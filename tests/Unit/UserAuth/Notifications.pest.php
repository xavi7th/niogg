<?php

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\UserAuth\Notifications\VerifyEmail;
use Modules\UserAuth\Notifications\SendPasswordResetNotification;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user receives password reset notification', function (): void {
    Notification::fake();

    $user = User::factory()->create();
    $user->sendPasswordResetNotification('test-token');

    Notification::assertSentTo($user, SendPasswordResetNotification::class, function ($notification) {
        expect($notification->token)->toBe('test-token');

        return TRUE;
    });
});

test('unverified user receives verify email notification', function (): void {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('verify email notification contains correct subject', function (): void {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();

    Notification::assertSentTo($user, VerifyEmail::class, function ($notification) use ($user) {
        $mailMessage = $notification->toMail($user);

        expect($mailMessage->subject)->toContain('Verify Email Address');

        return TRUE;
    });
});
