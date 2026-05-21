<?php

use Illuminate\Support\Facades\Bus;
use Modules\PublicPage\Models\EventPhoto;

test('retries photos with null thumbnail', function (): void {
    Bus::fake();

    EventPhoto::factory()->create(['thumbnail_url' => NULL]);

    $this->artisan('photos:retry-failed-thumbnails')->assertSuccessful();

    Bus::assertDispatched(Modules\PublicPage\Jobs\GeneratePhotoThumbnail::class);
});

test('retries photos with failed generation', function (): void {
    Bus::fake();

    EventPhoto::factory()->create([
        'thumbnail_url' => 'some-url.jpg',
        'thumbnail_generation' => 'failed',
    ]);

    $this->artisan('photos:retry-failed-thumbnails')->assertSuccessful();

    Bus::assertDispatched(Modules\PublicPage\Jobs\GeneratePhotoThumbnail::class);
});

test('skips photos with successful thumbnails', function (): void {
    Bus::fake();

    EventPhoto::factory()->create([
        'thumbnail_url' => 'thumbs/success.jpg',
        'thumbnail_generation' => NULL,
    ]);

    $this->artisan('photos:retry-failed-thumbnails')->assertSuccessful();

    Bus::assertNothingDispatched();
});

test('handles empty database gracefully', function (): void {
    $this->artisan('photos:retry-failed-thumbnails')->assertSuccessful();
});
