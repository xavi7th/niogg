<?php

use App\Models\User;
use Modules\PublicPage\Models\Event;

test('admin can access event edit page', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.events.edit', $event));

    $response->assertStatus(200);
});

test('non admin cannot access event edit page', function (): void {
    $event = Event::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.events.edit', $event));

    $response->assertForbidden();
});

test('unauthenticated user cannot access event edit page', function (): void {
    $event = Event::factory()->create();

    $response = $this->get(route('admin.events.edit', $event));

    $response->assertRedirect(route('auth.login'));
});
