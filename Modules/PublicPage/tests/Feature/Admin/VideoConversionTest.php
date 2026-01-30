<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Jobs\ConvertVideoToMp4;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Services\VideoUploadService;

class VideoConversionTest extends TestCase
{
  use RefreshDatabase;

  private VideoUploadService $uploadService;

  protected function setUp(): void
  {
    parent::setUp();
    $this->uploadService = app(VideoUploadService::class);
    Storage::fake('public');
    Queue::fake();
  }

  public function test_webm_video_dispatches_conversion_job_on_finalize(): void
  {
    $event = Event::factory()->create();
    $uploadId = \Illuminate\Support\Str::uuid()->toString();

    cache()->put("upload:{$uploadId}", [
      'upload_id' => $uploadId,
      'event_id' => $event->id,
      'original_filename' => 'test-video.webm',
      'mime_type' => 'video/webm',
      'total_size' => 10485760,
      'chunks_received' => 2,
      'bytes_received' => 10485760,
      'status' => 'complete',
    ], now()->addHours(24));

    Storage::disk('public')->makeDirectory('videos/chunks/' . $uploadId);
    Storage::disk('public')->put('videos/chunks/' . $uploadId . '/chunk_0', 'fake video data part 1');
    Storage::disk('public')->put('videos/chunks/' . $uploadId . '/chunk_1', 'fake video data part 2');

    $this->uploadService->finalizeUpload($uploadId, [
      'title' => 'Test Video',
      'duration_seconds' => 120,
    ]);

    $video = Video::where('upload_id', $uploadId)->first();

    $this->assertNotNull($video);
    $this->assertEquals('video/webm', $video->mime_type);
    $this->assertEquals('pending', $video->conversion_status);

    Queue::assertPushed(ConvertVideoToMp4::class, fn($job) => $job->video->id === $video->id);
  }

  public function test_mp4_video_does_not_dispatch_conversion_job(): void
  {
    $event = Event::factory()->create();
    $uploadId = \Illuminate\Support\Str::uuid()->toString();

    cache()->put("upload:{$uploadId}", [
      'upload_id' => $uploadId,
      'event_id' => $event->id,
      'original_filename' => 'test-video.mp4',
      'mime_type' => 'video/mp4',
      'total_size' => 10485760,
      'chunks_received' => 2,
      'bytes_received' => 10485760,
      'status' => 'complete',
    ], now()->addHours(24));

    Storage::disk('public')->makeDirectory('videos/chunks/' . $uploadId);
    Storage::disk('public')->put('videos/chunks/' . $uploadId . '/chunk_0', 'fake video data part 1');
    Storage::disk('public')->put('videos/chunks/' . $uploadId . '/chunk_1', 'fake video data part 2');

    $this->uploadService->finalizeUpload($uploadId, [
      'title' => 'Test Video',
      'duration_seconds' => 120,
    ]);

    $video = Video::where('upload_id', $uploadId)->first();

    $this->assertNotNull($video);
    $this->assertEquals('video/mp4', $video->mime_type);
    $this->assertEquals('completed', $video->conversion_status);

    Queue::assertNotPushed(ConvertVideoToMp4::class);
  }

  public function test_mov_video_dispatches_conversion_job(): void
  {
    $event = Event::factory()->create();
    $uploadId = \Illuminate\Support\Str::uuid()->toString();

    cache()->put("upload:{$uploadId}", [
      'upload_id' => $uploadId,
      'event_id' => $event->id,
      'original_filename' => 'test-video.mov',
      'mime_type' => 'video/quicktime',
      'total_size' => 10485760,
      'chunks_received' => 2,
      'bytes_received' => 10485760,
      'status' => 'complete',
    ], now()->addHours(24));

    Storage::disk('public')->makeDirectory('videos/chunks/' . $uploadId);
    Storage::disk('public')->put('videos/chunks/' . $uploadId . '/chunk_0', 'fake video data part 1');
    Storage::disk('public')->put('videos/chunks/' . $uploadId . '/chunk_1', 'fake video data part 2');

    $this->uploadService->finalizeUpload($uploadId, [
      'title' => 'Test Video',
      'duration_seconds' => 120,
    ]);

    Queue::assertPushed(ConvertVideoToMp4::class);
  }

  public function test_conversion_job_has_unique_id(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'mime_type' => 'video/webm',
      'conversion_status' => 'pending',
    ]);

    $job = new ConvertVideoToMp4($video);

    $this->assertEquals("video:{$video->id}:convert-to-mp4", $job->uniqueId());
  }

  public function test_conversion_job_uses_correct_queue(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'mime_type' => 'video/webm',
      'conversion_status' => 'pending',
    ]);

    $job = new ConvertVideoToMp4($video);

    $this->assertEquals('video-conversion', $job->queue);
  }

  public function test_video_model_casts_conversion_timestamps(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'mime_type' => 'video/webm',
      'conversion_status' => 'completed',
      'conversion_started_at' => '2026-01-29 12:00:00',
      'conversion_completed_at' => '2026-01-29 12:05:00',
    ]);

    $this->assertInstanceOf(\Carbon\Carbon::class, $video->conversion_started_at);
    $this->assertInstanceOf(\Carbon\Carbon::class, $video->conversion_completed_at);
  }

  public function test_conversion_status_can_be_pending(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'mime_type' => 'video/webm',
      'conversion_status' => 'pending',
    ]);

    $this->assertEquals('pending', $video->conversion_status);
    $this->assertNull($video->conversion_started_at);
    $this->assertNull($video->conversion_completed_at);
  }

  public function test_conversion_status_enum_values(): void
  {
    $event = Event::factory()->create();
    $statuses = ['pending', 'converting', 'completed', 'failed'];

    foreach ($statuses as $status) {
      $video = Video::factory()->for($event)->create([
        'mime_type' => 'video/webm',
        'conversion_status' => $status,
      ]);

      $this->assertEquals($status, $video->conversion_status);
    }
  }
}
