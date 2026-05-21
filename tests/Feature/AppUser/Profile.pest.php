<?php

use App\Models\User;

test('profile page is displayed', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);
    $this->post(route('auth.password.confirm'), [
        'password' => 'password',
    ]);

    $response = $this->get(route('appuser.profile.edit'));

    $response->assertStatus(200);
});

test('profile information can be updated', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('appuser.profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email is unchanged', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => $user->email,
    ]);

    $response->assertSessionHasNoErrors();
    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('unauthenticated user cannot access profile', function (): void {
    $response = $this->get(route('appuser.profile.edit'));

    $response->assertRedirect(route('auth.login'));
});

test('profile update requires valid email', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('profile update requires unique email', function (): void {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => 'existing@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('user can delete their account', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('appuser.profile.destroy'), [
        'password' => 'password',
    ]);

    $response->assertSessionHasNoErrors();
    expect(auth()->check())->toBeFalse()
        ->and(User::find($user->id))->toBeNull();
});

test('correct password must be provided to delete account', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('appuser.profile.destroy'), [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['password']);
    expect(User::find($user->id))->not->toBeNull();
});
