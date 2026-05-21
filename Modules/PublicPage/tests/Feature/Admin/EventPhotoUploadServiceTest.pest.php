<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Services\EventPhotoUploadService;

uses(Modules\PublicPage\Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function urlToPath(?string $url): ?string
{
    if (empty($url)) {
        return NULL;
    }

    return ltrim(str_replace(Storage::disk('public')->url(''), '', $url), '/');
}

beforeEach(function (): void {
    Storage::fake('public');
});

test('store saves photo and dispatches thumbnail job', function (): void {
    Bus::fake();

    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');
    $service = app(EventPhotoUploadService::class);

    $photo = $service->store($file, $event, 1);

    expect($photo->event_id)->toBe($event->id)
        ->and($photo->photo_url)->not->toBeNull()
        ->and($photo->sort_order)->toBe(1);

    Bus::assertDispatched(Modules\PublicPage\Jobs\GeneratePhotoThumbnail::class);
});

test('store generates unique filename', function (): void {
    $event = Event::factory()->create();
    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');
    $service = app(EventPhotoUploadService::class);

    $photo1 = $service->store($file1, $event, 1);
    $photo2 = $service->store($file2, $event, 2);

    expect($photo1->photo_url)->not->toBe($photo2->photo_url);
});

test('generate thumbnail creates thumbnail file', function (): void {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);
    $service = app(EventPhotoUploadService::class);

    $photo = $service->store($file, $event, 1);

    $service->generateThumbnail($photo);
    $photo->refresh();

    expect($photo->thumbnail_url)->not->toBeNull();
    Storage::disk('public')->assertExists(urlToPath($photo->thumbnail_url));
});

test('delete removes photo and thumbnail', function (): void {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');
    $service = app(EventPhotoUploadService::class);

    $photo = $service->store($file, $event, 1);
    $service->generateThumbnail($photo);
    $photo->refresh();

    $photoPath = urlToPath($photo->photo_url);
    $thumbPath = urlToPath($photo->thumbnail_url);

    Storage::disk('public')->assertExists($photoPath);
    Storage::disk('public')->assertExists($thumbPath);

    $service->delete($photo);

    Storage::disk('public')->assertMissing($photoPath);
    Storage::disk('public')->assertMissing($thumbPath);
});

test('delete handles missing files gracefully', function (): void {
    $photo = EventPhoto::factory()->create([
        'photo_url' => '/storage/event-photos/nonexistent.jpg',
        'thumbnail_url' => NULL,
    ]);

    $service = app(EventPhotoUploadService::class);

    // Should not throw
    $service->delete($photo);
});
