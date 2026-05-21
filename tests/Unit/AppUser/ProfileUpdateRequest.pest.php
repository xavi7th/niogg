<?php

use App\Models\User;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('rules require name and email', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->patch(route('appuser.profile.update'), [
        'name' => '',
        'email' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'email']);
});

test('rules require valid email format', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['email']);
});

test('rules require unique email', function (): void {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->patch(route('appuser.profile.update'), [
        'name' => 'Test User',
        'email' => 'existing@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
});
