<?php

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;

uses(Modules\PublicPage\Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

test('photo belongs to event relationship', function (): void {
    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->create(['event_id' => $event->id]);

    expect($photo->event->id)->toBe($event->id);
});

test('ordered scope orders by sort order', function (): void {
    $event = Event::factory()->create();
    $first = EventPhoto::factory()->create(['event_id' => $event->id, 'sort_order' => 1]);
    $second = EventPhoto::factory()->create(['event_id' => $event->id, 'sort_order' => 2]);

    $results = EventPhoto::ordered()->get();

    expect($results->first()->id)->toBe($first->id)
        ->and($results->last()->id)->toBe($second->id);
});
