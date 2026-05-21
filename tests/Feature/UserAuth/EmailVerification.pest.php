<?php

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;

test('email verification screen can be rendered', function (): void {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('auth.verification.notice'));

    $response->assertStatus(200);
});

test('email can be verified', function (): void {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'auth.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('email is not verified with invalid hash', function (): void {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'auth.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verification notification can be resent', function (): void {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post(route('auth.verification.send'));

    expect($user->exists)->toBeTrue();
});
