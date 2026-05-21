<?php

namespace Modules\PublicPage\Tests\Unit\Models;

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    Storage::fake('public');
  }

  public function test_get_thumbnail_url_attribute_with_custom_thumbnail(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'custom_thumbnail_url' => 'thumbs/custom.jpg',
      'thumbnail_url' => 'thumbs/auto.jpg',
    ]);

    $this->assertStringContainsString('custom.jpg', $video->thumbnail_url);
  }

  public function test_get_thumbnail_url_attribute_fallback_to_auto_thumbnail(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'custom_thumbnail_url' => NULL,
      'thumbnail_url' => 'thumbs/auto.jpg',
    ]);

    $this->assertStringContainsString('auto.jpg', $video->thumbnail_url);
  }

  public function test_get_thumbnail_url_attribute_fallback_to_placeholder(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'custom_thumbnail_url' => NULL,
      'thumbnail_url' => NULL,
    ]);

    $this->assertStringContainsString('placeholder', $video->thumbnail_url);
  }

  public function test_thumbnail_url_includes_cache_buster_for_auto_thumbnails(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'custom_thumbnail_url' => NULL,
      'thumbnail_url' => 'thumbs/auto.jpg',
      'updated_at' => now(),
    ]);

    $thumbnailUrl = $video->thumbnail_url;
    $this->assertStringContainsString('?', $thumbnailUrl);
  }

  public function test_format_duration_accessor(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create(['duration_seconds' => 3661]);

    $this->assertEquals('61:01', $video->format_duration);
  }

  public function test_format_duration_handles_zero(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create(['duration_seconds' => 0]);

    $this->assertEquals('0:00', $video->format_duration);
  }

  public function test_featured_scope(): void
  {
    $event = Event::factory()->create();
    $featured = Video::factory()->for($event)->create(['is_featured' => TRUE]);
    $normal = Video::factory()->for($event)->create(['is_featured' => FALSE]);

    $results = Video::featured()->get();

    $this->assertTrue($results->contains($featured));
    $this->assertFalse($results->contains($normal));
  }

  public function test_event_relationship(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();

    $this->assertEquals($event->id, $video->event->id);
  }
}
