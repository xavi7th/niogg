<?php

use App\Models\User;
use Modules\PublicPage\Models\EventPhoto;

test('update event photo request validates alt text max length', function (): void {
    $photo = EventPhoto::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->put(route('admin.photos.update', $photo), [
        'alt_text' => str_repeat('a', 256),
    ]);

    $response->assertSessionHasErrors(['alt_text']);
});

test('update event photo request passes with valid data', function (): void {
    $photo = EventPhoto::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->put(route('admin.photos.update', $photo), [
        'alt_text' => 'Valid alt text',
    ]);

    $response->assertSessionHasNoErrors();
});

test('update event photo request allows null alt text', function (): void {
    $photo = EventPhoto::factory()->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->put(route('admin.photos.update', $photo), [
        'alt_text' => NULL,
    ]);

    $response->assertSessionHasNoErrors();
});
