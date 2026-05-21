<?php

use App\Models\User;

test('confirm password screen can be rendered', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('auth.password.confirm'));

    $response->assertStatus(200);
});

test('password can be confirmed', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('auth.password.confirm'), [
        'password' => 'password',
    ]);

    $response->assertRedirect()
        ->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('auth.password.confirm'), [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
});
