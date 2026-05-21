<?php

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;

uses(Modules\PublicPage\Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('featured scope returns only featured videos', function (): void {
    $event = Event::factory()->create();
    $featured = Video::factory()->create(['event_id' => $event->id, 'is_featured' => TRUE]);
    $regular = Video::factory()->create(['event_id' => $event->id, 'is_featured' => FALSE]);

    $results = Video::featured()->get();

    expect($results->pluck('id'))->toContain($featured->id)
        ->and($results->pluck('id'))->not->toContain($regular->id);
});

test('video belongs to event relationship', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->create(['event_id' => $event->id]);

    expect($video->event->id)->toBe($event->id);
});

test('format duration accessor formats seconds correctly', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create(['duration_seconds' => 3661]);

    expect($video->format_duration)->toBe('61:01');
});

test('format duration accessor handles zero duration', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create(['duration_seconds' => 0]);

    expect($video->format_duration)->toBe('0:00');
});

test('format duration accessor handles minutes only', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create(['duration_seconds' => 120]);

    expect($video->format_duration)->toBe('2:00');
});

test('thumbnail url accessor returns custom thumbnail when set', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
        'custom_thumbnail_url' => 'thumbs/custom.jpg',
        'thumbnail_url' => 'thumbs/auto.jpg',
    ]);

    expect($video->thumbnail_url)->toContain('custom.jpg');
});

test('thumbnail url accessor falls back to auto thumbnail', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
        'custom_thumbnail_url' => NULL,
        'thumbnail_url' => 'thumbs/auto.jpg',
    ]);

    expect($video->thumbnail_url)->toContain('auto.jpg');
});

test('thumbnail url accessor falls back to placeholder', function (): void {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
        'custom_thumbnail_url' => NULL,
        'thumbnail_url' => NULL,
    ]);

    expect($video->thumbnail_url)->toBe('/images/video-placeholder-default.jpg');
});
