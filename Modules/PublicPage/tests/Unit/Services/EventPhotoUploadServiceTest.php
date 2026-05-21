<?php

namespace Modules\PublicPage\Tests\Unit\Services;

use Throwable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Services\EventPhotoUploadService;

class EventPhotoUploadServiceTest extends TestCase
{
  use RefreshDatabase;

  private EventPhotoUploadService $service;

  protected function setUp(): void
  {
    parent::setUp();
    $this->service = new EventPhotoUploadService();
    Storage::fake('public');
    Bus::fake();
  }

  public function test_store_creates_photo_record(): void
  {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

    $photo = $this->service->store($file, $event, 0);

    $this->assertInstanceOf(EventPhoto::class, $photo);
    $this->assertNotNull($photo->photo_url);
    $this->assertNull($photo->thumbnail_url);
    $this->assertEquals(0, $photo->sort_order);
  }

  public function test_store_sets_correct_sort_order(): void
  {
    $event = Event::factory()->create();
    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');

    $photo1 = $this->service->store($file1, $event, 0);
    $photo2 = $this->service->store($file2, $event, 1);

    $this->assertEquals(0, $photo1->sort_order);
    $this->assertEquals(1, $photo2->sort_order);
  }

  public function test_store_dispatches_thumbnail_job(): void
  {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $this->service->store($file, $event, 0);

    Bus::assertDispatched(\Modules\PublicPage\Jobs\GeneratePhotoThumbnail::class);
  }

  public function test_delete_removes_files(): void
  {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');
    $photo = $this->service->store($file, $event, 0);

    $photoPath = $this->urlToRelativePath($photo->photo_url);
    Storage::disk('public')->assertExists($photoPath);

    $this->service->delete($photo);

    Storage::disk('public')->assertMissing($photoPath);
  }

  public function test_delete_handles_null_urls(): void
  {
    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->for($event)->create();

    $exception = NULL;
    try {
      $this->service->delete($photo);
    } catch (Throwable $e) {
      $exception = $e;
    }

    $this->assertNull($exception, 'Expected no exception when deleting photo');
  }

  public function test_generate_thumbnail_early_returns_for_missing_file(): void
  {
    $event = Event::factory()->create();
    $photo = EventPhoto::factory()->for($event)->create();

    $this->service->generateThumbnail($photo);

    $this->assertNotNull($photo->refresh()->thumbnail_url);
  }

  private function urlToRelativePath(string $url): string
  {
    return ltrim(str_replace('/storage/', '', $url), '/');
  }
}
