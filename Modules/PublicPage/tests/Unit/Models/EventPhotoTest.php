<?php

namespace Modules\PublicPage\Tests\Unit\Models;

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Tests\TestCase;
use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventPhotoTest extends TestCase
{
  use RefreshDatabase;

  public function test_ordered_scope_returns_photos_in_correct_order(): void
  {
    $event = Event::factory()->create();
    $photo1 = EventPhoto::factory()->for($event)->create(['sort_order' => 2]);
    $photo2 = EventPhoto::factory()->for($event)->create(['sort_order' => 1]);
    $photo3 = EventPhoto::factory()->for($event)->create(['sort_order' => 3]);

    $results = EventPhoto::ordered()->get();

    $this->assertEquals($photo2->id, $results->first()->id);
    $this->assertEquals($photo3->id, $results->last()->id);
  }

  public function test_event_relationship(): void
  {
    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->for($event)->create();

    $this->assertEquals($event->id, $photo->event->id);
  }

  public function test_cascade_delete_removes_photos(): void
  {
    $event = Event::factory()->create();
    EventPhoto::factory()->count(3)->for($event)->create();

    $this->assertEquals(3, EventPhoto::count());

    $event->delete();

    $this->assertEquals(0, EventPhoto::count());
  }
}
