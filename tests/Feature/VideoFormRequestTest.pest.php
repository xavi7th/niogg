<?php

use App\Models\User;
use Modules\PublicPage\Models\Event;

test('video form request requires title', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.videos.store', $event), [
        'title' => '',
        'video_url' => 'https://example.com/video.mp4',
    ]);

    $response->assertSessionHasErrors(['title']);
});

test('video form request requires valid url', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.videos.store', $event), [
        'title' => 'Test Video',
        'video_url' => 'not-a-url',
    ]);

    $response->assertSessionHasErrors(['video_url']);
});

test('video form request passes with valid data', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.videos.store', $event), [
        'title' => 'Test Video',
        'video_url' => 'https://example.com/video.mp4',
    ]);

    $response->assertSessionHasNoErrors();
});

test('video form request allows nullable description', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.videos.store', $event), [
        'title' => 'Test Video',
        'video_url' => 'https://example.com/video.mp4',
        'description' => NULL,
    ]);

    $response->assertSessionHasNoErrors();
});

test('video form request validates duration is positive integer', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.videos.store', $event), [
        'title' => 'Test Video',
        'video_url' => 'https://example.com/video.mp4',
        'duration_seconds' => -1,
    ]);

    $response->assertSessionHasErrors(['duration_seconds']);
});
