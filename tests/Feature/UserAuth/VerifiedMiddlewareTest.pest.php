<?php

use App\Models\User;

test('unverified user is redirected from dashboard', function (): void {
    $user = User::factory()->create(['email_verified_at' => NULL]);

    $response = $this->actingAs($user)->get(route('appuser.dashboard'));

    $response->assertRedirect(route('auth.verification.notice'));
});

test('verified user can access dashboard', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('appuser.dashboard'));

    $response->assertStatus(200);
});
