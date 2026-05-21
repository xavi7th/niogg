<?php

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\UserAuth\Notifications\SendPasswordResetNotification;

test('reset password link screen can be rendered', function (): void {
    $response = $this->get(route('auth.password.request'));

    $response->assertStatus(200);
});

test('reset password link can be requested', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('auth.password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, SendPasswordResetNotification::class);
});

test('reset password link cannot be requested for nonexistent email', function (): void {
    Notification::fake();

    $this->post(route('auth.password.email'), ['email' => 'nonexistent@example.com']);

    Notification::assertNothingSent();
});

test('reset password screen can be rendered with valid token', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('auth.password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, SendPasswordResetNotification::class, function ($notification) use ($user): bool {
        $response = $this->get(route('auth.password.reset', [
            'token' => $notification->token,
            'email' => $user->email,
        ]));

        $response->assertStatus(200);

        return TRUE;
    });
});

test('password can be reset with valid token', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('auth.password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, SendPasswordResetNotification::class, function ($notification) use ($user): bool {
        $response = $this->post(route('auth.password.store'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('auth.login'));

        return TRUE;
    });
});

test('password cannot be reset with mismatched confirmation', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('auth.password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, SendPasswordResetNotification::class, function ($notification) use ($user): bool {
        $response = $this->post(route('auth.password.store'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password']);

        return TRUE;
    });
});
