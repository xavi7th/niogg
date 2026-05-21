<?php

namespace Modules\PublicPage\Tests\Unit\Services;

use Throwable;
use InvalidArgumentException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Services\VideoUploadService;
use Modules\PublicPage\Services\VideoThumbnailService;

class VideoUploadServiceTest extends TestCase
{
  use RefreshDatabase;

  private VideoUploadService $service;

  protected function setUp(): void
  {
    parent::setUp();
    $thumbnailService = app(VideoThumbnailService::class);
    $this->service = new VideoUploadService($thumbnailService);
    Storage::fake('public');
    Bus::fake();
  }

  public function test_initialize_upload_creates_session(): void
  {
    $event = Event::factory()->create();

    $result = $this->service->initializeUpload('test-video.mp4', 5242880, 'video/mp4', $event->id);

    $this->assertArrayHasKey('upload_id', $result);
    $this->assertArrayHasKey('chunk_size', $result);
    $this->assertArrayHasKey('total_chunks', $result);
    $this->assertEquals(10485760, $result['chunk_size']);
    $this->assertEquals(1, $result['total_chunks']);
    $this->assertTrue(cache()->has('upload:' . $result['upload_id']));
  }

  public function test_initialize_upload_rejects_invalid_mime_type(): void
  {
    $event = Event::factory()->create();

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid file type');

    $this->service->initializeUpload('test.avi', 5242880, 'video/avi', $event->id);
  }

  public function test_initialize_upload_rejects_oversized_file(): void
  {
    $event = Event::factory()->create();

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('File size exceeds maximum');

    $this->service->initializeUpload('test.mp4', 2000000000, 'video/mp4', $event->id);
  }

  public function test_upload_chunk_stores_chunk(): void
  {
    $event = Event::factory()->create();
    $init = $this->service->initializeUpload('test-video.mp4', 1024, 'video/mp4', $event->id);

    $file = UploadedFile::fake()->create('chunk.mp4', 1);
    $result = $this->service->uploadChunk($init['upload_id'], $file, 0, 1);

    $this->assertTrue($result['progress'] >= 0);
    $this->assertEquals(1, $result['chunks_received']);
    $this->assertEquals('complete', $result['status']);
  }

  public function test_upload_chunk_rejects_invalid_session(): void
  {
    $file = UploadedFile::fake()->create('chunk.mp4', 1);

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid upload ID');

    $this->service->uploadChunk('invalid-session', $file, 0, 1);
  }

  public function test_upload_chunk_tracks_progress(): void
  {
    $event = Event::factory()->create();
    $chunkSizeKb = 5120;
    $totalSize = $chunkSizeKb * 1024 * 3;
    $init = $this->service->initializeUpload('test-video.mp4', $totalSize, 'video/mp4', $event->id);

    $file1 = UploadedFile::fake()->create('chunk0.mp4', $chunkSizeKb);
    $file2 = UploadedFile::fake()->create('chunk1.mp4', $chunkSizeKb);

    $result1 = $this->service->uploadChunk($init['upload_id'], $file1, 0, 3);
    $result2 = $this->service->uploadChunk($init['upload_id'], $file2, 1, 3);

    $this->assertGreaterThanOrEqual(0, $result1['progress']);
    $this->assertEquals(2, $result2['chunks_received']);
  }

  public function test_finalize_upload_creates_video(): void
  {
    $event = Event::factory()->create();
    $init = $this->service->initializeUpload('test-video.mp4', 1024, 'video/mp4', $event->id);

    $file = UploadedFile::fake()->create('chunk.mp4', 1);
    $this->service->uploadChunk($init['upload_id'], $file, 0, 1);

    $video = $this->service->finalizeUpload($init['upload_id'], [
      'title' => 'Test Video',
      'description' => 'Test description',
      'thumbnail_url' => 'thumbs/test.jpg',
    ]);

    $this->assertInstanceOf(Video::class, $video);
    $this->assertEquals('Test Video', $video->title);
    $this->assertNotNull($video->video_url);
    $this->assertEquals('completed', $video->conversion_status);
  }

  public function test_finalize_upload_clears_cache(): void
  {
    $event = Event::factory()->create();
    $init = $this->service->initializeUpload('test-video.mp4', 1024, 'video/mp4', $event->id);

    $file = UploadedFile::fake()->create('chunk.mp4', 1);
    $this->service->uploadChunk($init['upload_id'], $file, 0, 1);

    $this->service->finalizeUpload($init['upload_id'], [
      'title' => 'Test',
      'thumbnail_url' => 'thumbs/test.jpg',
    ]);

    $this->assertFalse(cache()->has('upload:' . $init['upload_id']));
  }

  public function test_finalize_upload_rejects_incomplete_upload(): void
  {
    $event = Event::factory()->create();
    $chunkSizeKb = 5120;
    $chunkSizeBytes = $chunkSizeKb * 1024;
    $totalSize = $chunkSizeBytes * 2 + 1;
    $init = $this->service->initializeUpload('test-video.mp4', $totalSize, 'video/mp4', $event->id);

    $file = UploadedFile::fake()->create('chunk.mp4', $chunkSizeKb);
    $this->service->uploadChunk($init['upload_id'], $file, 0, 3);

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Upload is not complete');

    $this->service->finalizeUpload($init['upload_id'], ['title' => 'Test']);
  }

  public function test_cancel_upload_clears_resources(): void
  {
    $event = Event::factory()->create();
    $init = $this->service->initializeUpload('test-video.mp4', 1024, 'video/mp4', $event->id);

    $this->service->cancelUpload($init['upload_id']);

    $this->assertFalse(cache()->has('upload:' . $init['upload_id']));
  }

  public function test_resume_upload_returns_missing_chunks(): void
  {
    $event = Event::factory()->create();
    $chunkSizeKb = 5120;
    $totalSize = $chunkSizeKb * 1024 * 2;
    $init = $this->service->initializeUpload('test-video.mp4', $totalSize, 'video/mp4', $event->id);

    $file = UploadedFile::fake()->create('chunk.mp4', $chunkSizeKb);
    $this->service->uploadChunk($init['upload_id'], $file, 0, 2);

    $result = $this->service->resumeUpload($init['upload_id']);

    $this->assertArrayHasKey('received_chunks', $result);
    $this->assertArrayHasKey('missing_chunks', $result);
    $this->assertContains(0, $result['received_chunks']);
  }

  public function test_delete_video_file_does_not_throw_when_file_missing(): void
  {
    $event = Event::factory()->create();
    $video = Video::factory()->for($event)->create([
      'video_url' => '/storage/videos/nonexistent.mp4',
    ]);

    $exception = NULL;
    try {
      $this->service->deleteVideoFile($video);
    } catch (Throwable $e) {
      $exception = $e;
    }

    $this->assertNull($exception, 'Expected no exception when deleting missing file');
  }
}
