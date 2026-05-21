<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;

test('video thumbnail request requires file', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.videos.thumbnail', $video));

    $response->assertSessionHasErrors(['thumbnail']);
});

test('video thumbnail request requires image file', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $admin = User::factory()->admin()->create();
    $file = UploadedFile::fake()->create('document.pdf');

    $response = $this->actingAs($admin)->post(route('admin.videos.thumbnail', $video), [
        'thumbnail' => $file,
    ]);

    $response->assertSessionHasErrors(['thumbnail']);
});

test('video thumbnail request passes with valid image', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $admin = User::factory()->admin()->create();
    $file = UploadedFile::fake()->image('thumbnail.jpg');

    $response = $this->actingAs($admin)->post(route('admin.videos.thumbnail', $video), [
        'thumbnail' => $file,
    ]);

    $response->assertSessionHasNoErrors();
});

test('video thumbnail request rejects oversized file', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $admin = User::factory()->admin()->create();
    $file = UploadedFile::fake()->image('huge.jpg')->size(6000); // 6MB, max is 5MB

    $response = $this->actingAs($admin)->post(route('admin.videos.thumbnail', $video), [
        'thumbnail' => $file,
    ]);

    $response->assertSessionHasErrors(['thumbnail']);
});
