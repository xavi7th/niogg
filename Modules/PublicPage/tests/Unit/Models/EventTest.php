<?php

namespace Modules\PublicPage\Tests\Unit\Models;

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;
use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventTest extends TestCase
{
  use RefreshDatabase;

  public function test_generate_unique_slug_without_collision(): void
  {
    $event = Event::factory()->create(['name' => 'Test Event']);

    $this->assertEquals('test-event', $event->slug);
  }

  public function test_generate_unique_slug_with_collision(): void
  {
    Event::factory()->create(['name' => 'Test Event', 'slug' => 'test-event']);

    $event = Event::factory()->create(['name' => 'Test Event']);

    $this->assertStringStartsWith('test-event-', $event->slug);
  }

  public function test_published_scope_returns_only_published_events(): void
  {
    $published = Event::factory()->create(['is_published' => TRUE]);
    $draft = Event::factory()->create(['is_published' => FALSE]);

    $results = Event::published()->get();

    $this->assertTrue($results->contains($published));
    $this->assertFalse($results->contains($draft));
  }

  public function test_ordered_scope_returns_events_in_correct_order(): void
  {
    $event1 = Event::factory()->create(['event_date' => now()->subDays(10), 'is_published' => TRUE]);
    $event2 = Event::factory()->create(['event_date' => now()->subDays(5), 'is_published' => TRUE]);
    $event3 = Event::factory()->create(['event_date' => now()->subDays(15), 'is_published' => TRUE]);

    $results = Event::published()->ordered('desc')->get();

    $this->assertEquals($event2->id, $results->first()->id);
  }

  public function test_videos_relationship_returns_videos(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();

    $this->assertTrue($event->videos->contains($video));
  }

  public function test_photos_relationship_returns_photos(): void
  {
    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->for($event)->create();

    $this->assertTrue($event->photos->contains($photo));
  }
}
