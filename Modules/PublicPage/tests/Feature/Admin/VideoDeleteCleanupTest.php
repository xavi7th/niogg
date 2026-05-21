<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Support\Str;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Jobs\ConvertVideoToMp4;
use Spatie\ResponseCache\Facades\ResponseCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Jobs\GenerateVideoThumbnail;
use Modules\PublicPage\Services\VideoUploadService;
use Modules\PublicPage\Services\VideoThumbnailService;

class VideoDeleteCleanupTest extends TestCase
{
    use RefreshDatabase;

    private VideoUploadService $uploadService;

    private VideoThumbnailService $thumbnailService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadService = app(VideoUploadService::class);
        $this->thumbnailService = app(VideoThumbnailService::class);
        Storage::fake('public');
    }

    public function test_deleting_video_removes_video_file_from_storage(): void
    {
        $event = Event::factory()->create();
        $videoPath = 'videos/test-uuid.mp4';
        Storage::disk('public')->put($videoPath, 'fake video content');

        $video = Video::factory()->for($event)->create([
          'video_url' => Storage::disk('public')->url($videoPath),
          'mime_type' => 'video/mp4',
          'upload_id' => 'test-uuid',
        ]);

        Storage::disk('public')->assertExists($videoPath);

        $video->delete();

        Storage::disk('public')->assertMissing($videoPath);
    }

    public function test_deleting_video_removes_converted_mp4_file(): void
    {
        $event = Event::factory()->create();
        $originalPath = 'videos/test-uuid.webm';
        $convertedPath = 'videos/test-uuid_converted.mp4';
        Storage::disk('public')->put($originalPath, 'fake webm content');
        Storage::disk('public')->put($convertedPath, 'fake mp4 content');

        $video = Video::factory()->for($event)->create([
          'video_url' => Storage::disk('public')->url($originalPath),
          'mime_type' => 'video/webm',
          'upload_id' => 'test-uuid',
        ]);

        Storage::disk('public')->assertExists($originalPath);
        Storage::disk('public')->assertExists($convertedPath);

        $video->delete();

        Storage::disk('public')->assertMissing($originalPath);
        Storage::disk('public')->assertMissing($convertedPath);
    }

    public function test_deleting_video_removes_auto_generated_thumbnails(): void
    {
        $event = Event::factory()->create();
        $uuid = Str::uuid()->toString();
        $sizes = ['small', 'medium', 'large'];

        foreach ($sizes as $size) {
            Storage::disk('public')->put('videos/thumbnails/' . $uuid . '_' . $size . '.jpg', 'fake ' . $size . ' thumbnail');
        }

        $video = Video::factory()->for($event)->create([
          'video_url' => Storage::disk('public')->url('videos/test.mp4'),
          'thumbnail_url' => Storage::disk('public')->url('videos/thumbnails/' . $uuid . '_medium.jpg'),
        ]);

        foreach ($sizes as $size) {
            Storage::disk('public')->assertExists('videos/thumbnails/' . $uuid . '_' . $size . '.jpg');
        }

        $video->delete();

        foreach ($sizes as $size) {
            Storage::disk('public')->assertMissing('videos/thumbnails/' . $uuid . '_' . $size . '.jpg');
        }
    }

    public function test_deleting_video_removes_custom_thumbnail(): void
    {
        $event = Event::factory()->create();
        $uuid = Str::uuid()->toString();
        $customPath = 'videos/thumbnails/custom/' . $uuid . '.png';
        Storage::disk('public')->put($customPath, 'fake custom thumbnail');

        $video = Video::factory()->for($event)->create([
          'video_url' => Storage::disk('public')->url('videos/test.mp4'),
          'custom_thumbnail_url' => Storage::disk('public')->url($customPath),
        ]);

        Storage::disk('public')->assertExists($customPath);

        $video->delete();

        Storage::disk('public')->assertMissing($customPath);
    }

    public function test_deleting_video_clears_response_cache(): void
    {
        $event = Event::factory()->create();
        $video = Video::factory()->for($event)->create();

        ResponseCache::shouldReceive('clear')->once();

        $video->delete();
    }

    public function test_convert_job_exits_gracefully_when_video_deleted(): void
    {
        $event = Event::factory()->create();
        $video = Video::factory()->for($event)->create([
          'mime_type' => 'video/webm',
        ]);

        $videoId = $video->id;
        $video->delete();

        $job = new ConvertVideoToMp4(Video::make(['id' => $videoId]));

        $this->expectNotToPerformAssertions();

        $job->handle();
    }

    public function test_thumbnail_job_exits_gracefully_when_video_deleted(): void
    {
        $event = Event::factory()->create();
        $video = Video::factory()->for($event)->create();

        $videoId = $video->id;
        $video->delete();

        $job = new GenerateVideoThumbnail(Video::make(['id' => $videoId]));

        $this->expectNotToPerformAssertions();

        $job->handle(app(VideoThumbnailService::class));
    }
}
