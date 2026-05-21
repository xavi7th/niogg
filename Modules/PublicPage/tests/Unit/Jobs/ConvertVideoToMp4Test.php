<?php

namespace Modules\PublicPage\Tests\Unit\Jobs;

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Queue;
use Modules\PublicPage\Tests\TestCase;
use Modules\PublicPage\Jobs\ConvertVideoToMp4;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConvertVideoToMp4Test extends TestCase
{
  use RefreshDatabase;

  public function test_job_can_be_instantiated(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'video_url' => '/storage/videos/test.webm',
    ]);

    $job = new ConvertVideoToMp4($video);

    $this->assertInstanceOf(ConvertVideoToMp4::class, $job);
    $this->assertEquals($video->id, $job->video->id);
  }

  public function test_job_dispatches_to_correct_queue(): void
  {
    Queue::fake();

    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'video_url' => '/storage/videos/test.webm',
    ]);

    ConvertVideoToMp4::dispatch($video);

    Queue::assertPushed(ConvertVideoToMp4::class, fn ($job) => $job->video->id === $video->id);
  }

  public function test_job_has_unique_id(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $job = new ConvertVideoToMp4($video);

    $uniqueId = $job->uniqueId();

    $this->assertStringContainsString((string) $video->id, $uniqueId);
    $this->assertStringContainsString('convert-to-mp4', $uniqueId);
  }

  public function test_job_has_unique_for_configured(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $job = new ConvertVideoToMp4($video);

    $this->assertEquals(3600, $job->uniqueFor);
  }

  public function test_job_has_middleware(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create();
    $job = new ConvertVideoToMp4($video);

    $middleware = $job->middleware();

    $this->assertNotEmpty($middleware);
  }
}
