<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Modules\PublicPage\Models\Event;

test('store event photos request requires files', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.events.photos.store', $event));

    $response->assertSessionHasErrors(['photos']);
});

test('store event photos request requires image files', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();
    $files = [UploadedFile::fake()->create('document.pdf')];

    $response = $this->actingAs($admin)->post(route('admin.events.photos.store', $event), [
        'photos' => $files,
    ]);

    $response->assertSessionHasErrors(['photos.0']);
});

test('store event photos request passes with valid images', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();
    $files = [
        UploadedFile::fake()->image('photo1.jpg'),
        UploadedFile::fake()->image('photo2.jpg'),
    ];

    $response = $this->actingAs($admin)->post(route('admin.events.photos.store', $event), [
        'photos' => $files,
    ]);

    $response->assertSessionHasNoErrors();
});

test('store event photos request limits to 20 photos', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();
    $files = array_map(fn ($i) => UploadedFile::fake()->image("photo{$i}.jpg"), range(1, 21));

    $response = $this->actingAs($admin)->post(route('admin.events.photos.store', $event), [
        'photos' => $files,
    ]);

    $response->assertSessionHasErrors(['photos']);
});

test('store event photos request rejects oversized files', function (): void {
    $event = Event::factory()->create();
    $admin = User::factory()->admin()->create();
    $file = UploadedFile::fake()->image('huge.jpg')->size(11000); // 11MB, max is 10MB

    $response = $this->actingAs($admin)->post(route('admin.events.photos.store', $event), [
        'photos' => [$file],
    ]);

    $response->assertSessionHasErrors(['photos.0']);
});
