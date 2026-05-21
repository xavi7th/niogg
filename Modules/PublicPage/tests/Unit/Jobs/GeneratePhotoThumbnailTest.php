<?php

namespace Modules\PublicPage\Tests\Unit\Jobs;

use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Queue;
use Modules\PublicPage\Tests\TestCase;
use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;

class GeneratePhotoThumbnailTest extends TestCase
{
  use RefreshDatabase;

  public function test_job_can_be_instantiated(): void
  {
    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->for($event)->create();
    $job = new GeneratePhotoThumbnail($photo);

    $this->assertInstanceOf(GeneratePhotoThumbnail::class, $job);
    $this->assertEquals($photo->id, $job->photo->id);
  }

  public function test_job_dispatches_correctly(): void
  {
    Queue::fake();

    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->for($event)->create();

    GeneratePhotoThumbnail::dispatch($photo);

    Queue::assertPushed(GeneratePhotoThumbnail::class);
  }
}
