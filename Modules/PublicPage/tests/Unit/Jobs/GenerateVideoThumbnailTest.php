<?php

namespace Modules\PublicPage\Tests\Unit\Jobs;

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Queue;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Jobs\GenerateVideoThumbnail;

class GenerateVideoThumbnailTest extends TestCase
{
  use RefreshDatabase;

  public function test_job_can_be_instantiated(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $job = new GenerateVideoThumbnail($video);

    $this->assertInstanceOf(GenerateVideoThumbnail::class, $job);
    $this->assertEquals($video->id, $job->video->id);
  }

  public function test_job_dispatches_correctly(): void
  {
    Queue::fake();

    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();

    GenerateVideoThumbnail::dispatch($video);

    Queue::assertPushed(GenerateVideoThumbnail::class);
  }

  public function test_job_has_retries_configured(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $job = new GenerateVideoThumbnail($video);

    $this->assertEquals(3, $job->tries);
  }

  public function test_job_has_timeout_configured(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $job = new GenerateVideoThumbnail($video);

    $this->assertEquals(120, $job->timeout);
  }
}
