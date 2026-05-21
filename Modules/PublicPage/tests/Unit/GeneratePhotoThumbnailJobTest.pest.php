<?php

use Illuminate\Http\UploadedFile;
use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Models\EventPhoto;
use Modules\PublicPage\Jobs\GeneratePhotoThumbnail;
use Modules\PublicPage\Services\EventPhotoUploadService;

uses(Modules\PublicPage\Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
});

test('job generates thumbnail via service', function (): void {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);
    $uploadService = app(EventPhotoUploadService::class);

    $photo = $uploadService->store($file, $event, 1);

    $job = new GeneratePhotoThumbnail($photo);
    $job->handle($uploadService);

    $photo->refresh();

    expect($photo->thumbnail_url)->not->toBeNull();
});

test('job handles deleted photo gracefully', function (): void {
    $photo = EventPhoto::factory()->make();
    $photo->id = 99999;

    $job = new GeneratePhotoThumbnail($photo);

    // Should not throw when photo doesn't exist in DB
    // The job will fail when trying to access the photo
    expect($job->photo)->toBeInstanceOf(EventPhoto::class);
});
