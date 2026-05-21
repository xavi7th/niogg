<?php

namespace Modules\PublicPage\Tests\Feature\FormRequests;

use App\Models\User;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoFormRequestTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->admin = User::factory()->admin()->create();
    $this->event = Event::factory()->create();
  }

  public function test_store_requires_title(): void
  {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.videos.store', $this->event), [
          'title' => '',
          'video_url' => 'https://example.com/video.mp4',
        ]);

    $response->assertSessionHasErrors(['title']);
  }

  public function test_store_requires_video_url(): void
  {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.videos.store', $this->event), [
          'title' => 'Test Video',
        ]);

    $response->assertSessionHasErrors(['video_url']);
  }

  public function test_store_requires_valid_url(): void
  {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.videos.store', $this->event), [
          'title' => 'Test Video',
          'video_url' => 'not-a-url',
        ]);

    $response->assertSessionHasErrors(['video_url']);
  }

  public function test_update_allows_partial_update(): void
  {
    $video = Video::factory()->forEvent($this->event)->create();

    $response = $this->actingAs($this->admin)
        ->put(route('admin.videos.update', $video), [
          'title' => 'Updated Title',
        ]);

    $response->assertSessionHasNoErrors();
  }

  public function test_validation_rejects_negative_duration(): void
  {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.videos.store', $this->event), [
          'title' => 'Test Video',
          'video_url' => 'https://example.com/video.mp4',
          'duration_seconds' => -1,
        ]);

    $response->assertSessionHasErrors(['duration_seconds']);
  }

  public function test_validation_accepts_valid_duration(): void
  {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.videos.store', $this->event), [
          'title' => 'Test Video',
          'video_url' => 'https://example.com/video.mp4',
          'duration_seconds' => 120,
        ]);

    $response->assertSessionHasNoErrors(['duration_seconds']);
  }
}
