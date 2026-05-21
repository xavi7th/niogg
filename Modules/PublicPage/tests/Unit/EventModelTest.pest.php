<?php

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Models\EventPhoto;

uses(Modules\PublicPage\Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('published scope returns only published events', function (): void {
    $published = Event::factory()->create(['is_published' => TRUE]);
    $draft = Event::factory()->create(['is_published' => FALSE]);

    $results = Event::published()->get();

    expect($results->pluck('id'))->toContain($published->id)
        ->and($results->pluck('id'))->not->toContain($draft->id);
});

test('unpublished scope returns only draft events', function (): void {
    $published = Event::factory()->create(['is_published' => TRUE]);
    $draft = Event::factory()->create(['is_published' => FALSE]);

    $results = Event::where('is_published', FALSE)->get();

    expect($results->pluck('id'))->toContain($draft->id)
        ->and($results->pluck('id'))->not->toContain($published->id);
});

test('ordered scope orders by date descending by default', function (): void {
    $older = Event::factory()->create(['event_date' => now()->subDays(10)]);
    $newer = Event::factory()->create(['event_date' => now()->subDays(5)]);

    $results = Event::ordered()->get();

    expect($results->first()->id)->toBe($newer->id)
        ->and($results->last()->id)->toBe($older->id);
});

test('ordered scope can order ascending', function (): void {
    $older = Event::factory()->create(['event_date' => now()->subDays(10)]);
    $newer = Event::factory()->create(['event_date' => now()->subDays(5)]);

    $results = Event::ordered('asc')->get();

    expect($results->first()->id)->toBe($older->id)
        ->and($results->last()->id)->toBe($newer->id);
});

test('event has many videos relationship', function (): void {
    $event = Event::factory()->create();
    $videos = Video::factory()->count(3)->create(['event_id' => $event->id]);

    expect($event->videos)->toHaveCount(3)
        ->and($event->videos->pluck('id'))->toContain($videos[0]->id);
});

test('event has many photos relationship', function (): void {
    $event = Event::factory()->create();
    $photos = EventPhoto::factory()->count(3)->create(['event_id' => $event->id]);

    expect($event->photos)->toHaveCount(3);
});

test('event slug is auto generated from name', function (): void {
    $event = Event::factory()->create(['name' => 'My Awesome Event']);

    expect($event->slug)->toBe('my-awesome-event');
});

test('event slug handles duplicate names', function (): void {
    Event::factory()->create(['name' => 'Conference']);
    $event2 = Event::factory()->create(['name' => 'Conference']);

    expect($event2->slug)->toBe('conference-2');
});
