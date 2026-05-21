<?php

use App\Models\User;

test('login screen can be rendered', function (): void {
    $response = $this->get(route('auth.login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function (): void {
    $user = User::factory()->create();

    $response = $this->post(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    expect(auth()->check())->toBeTrue();
    $response->assertRedirect(route('appuser.dashboard'));
});

test('users cannot authenticate with invalid password', function (): void {
    $user = User::factory()->create();

    $this->post(route('auth.login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    expect(auth()->check())->toBeFalse();
});

test('users can logout', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('auth.logout'));

    expect(auth()->check())->toBeFalse();
    $response->assertRedirect('/');
});

test('login redirects admins to admin dashboard', function (): void {
    $admin = User::factory()->admin()->create();

    $response = $this->post(route('auth.login'), [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    expect(auth()->check())->toBeTrue();
    $response->assertRedirect(route('admin.dashboard'));
});
