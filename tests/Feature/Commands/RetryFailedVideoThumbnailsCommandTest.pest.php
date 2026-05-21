<?php

use Illuminate\Support\Facades\Bus;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;

test('retries videos with null thumbnails', function (): void {
    Bus::fake();

    $event = Event::factory()->create();
    Video::factory()->for($event)->create([
        'thumbnail_url' => NULL,
        'custom_thumbnail_url' => NULL,
    ]);

    $this->artisan('videos:retry-thumbnails')->assertSuccessful();

    Bus::assertDispatched(Modules\PublicPage\Jobs\GenerateVideoThumbnail::class);
});

test('skips videos with existing thumbnails', function (): void {
    Bus::fake();

    $event = Event::factory()->create();
    Video::factory()->for($event)->create([
        'thumbnail_url' => 'thumbs/existing.jpg',
    ]);

    $this->artisan('videos:retry-thumbnails')->assertSuccessful();

    Bus::assertNothingDispatched();
});

test('skips videos with custom thumbnails', function (): void {
    Bus::fake();

    $event = Event::factory()->create();
    Video::factory()->for($event)->create([
        'thumbnail_url' => NULL,
        'custom_thumbnail_url' => 'thumbs/custom.jpg',
    ]);

    $this->artisan('videos:retry-thumbnails')->assertSuccessful();

    Bus::assertNothingDispatched();
});

test('handles empty database gracefully', function (): void {
    $this->artisan('videos:retry-thumbnails')->assertSuccessful();
});
