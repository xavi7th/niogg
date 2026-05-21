<?php

use App\Models\User;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;

test('admin can access video edit page', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.videos.edit', $video));

    $response->assertStatus(200);
});

test('non admin cannot access video edit page', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.videos.edit', $video));

    $response->assertForbidden();
});

test('unauthenticated user cannot access video edit page', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();

    $response = $this->get(route('admin.videos.edit', $video));

    $response->assertRedirect(route('auth.login'));
});
