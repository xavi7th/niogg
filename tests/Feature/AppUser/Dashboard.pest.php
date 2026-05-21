<?php

use App\Models\User;

test('dashboard page is displayed for authenticated user', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('appuser.dashboard'));

    $response->assertStatus(200);
});

test('unauthenticated user cannot access dashboard', function (): void {
    $response = $this->get(route('appuser.dashboard'));

    $response->assertRedirect(route('auth.login'));
});
