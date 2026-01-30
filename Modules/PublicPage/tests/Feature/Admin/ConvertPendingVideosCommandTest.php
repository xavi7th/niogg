<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Jobs\ConvertVideoToMp4;
use Illuminate\Support\Facades\Bus;

class ConvertPendingVideosCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Bus::fake();
    }

    public function test_command_finds_non_mp4_videos_with_pending_status(): void
    {
        $event = Event::factory()->create();

        Storage::disk('public')->put('videos/webm-test.webm', 'fake video content');

        $webmVideo = Video::factory()->for($event)->create([
            'title' => 'WebM Video',
            'mime_type' => 'video/webm',
            'conversion_status' => 'pending',
            'video_url' => Storage::disk('public')->url('videos/webm-test.webm'),
        ]);

        Artisan::call('videos:convert-pending');

        Bus::assertDispatched(ConvertVideoToMp4::class, function ($job) use ($webmVideo) {
            return $job->video->id === $webmVideo->id;
        });
    }

    public function test_command_finds_non_mp4_videos_with_null_conversion_status(): void
    {
        $event = Event::factory()->create();

        Storage::disk('public')->put('videos/test.mov', 'fake video content');

        $movVideo = Video::factory()->for($event)->create([
            'title' => 'MOV Video',
            'mime_type' => 'video/quicktime',
            'conversion_status' => NULL,
            'video_url' => Storage::disk('public')->url('videos/test.mov'),
        ]);

        Artisan::call('videos:convert-pending');

        Bus::assertDispatched(ConvertVideoToMp4::class);
    }

    public function test_command_skips_already_converting_videos(): void
    {
        $event = Event::factory()->create();

        Video::factory()->for($event)->create([
            'mime_type' => 'video/webm',
            'conversion_status' => 'converting',
        ]);

        Artisan::call('videos:convert-pending');

        Bus::assertNotDispatched(ConvertVideoToMp4::class);
    }

    public function test_command_skips_completed_videos(): void
    {
        $event = Event::factory()->create();

        Video::factory()->for($event)->create([
            'mime_type' => 'video/webm',
            'conversion_status' => 'completed',
        ]);

        Artisan::call('videos:convert-pending');

        Bus::assertNotDispatched(ConvertVideoToMp4::class);
    }

    public function test_dry_run_does_not_dispatch_jobs(): void
    {
        $event = Event::factory()->create();

        Storage::disk('public')->put('videos/test.webm', 'fake video content');

        Video::factory()->for($event)->create([
            'title' => 'Test Video',
            'mime_type' => 'video/webm',
            'conversion_status' => 'pending',
            'video_url' => Storage::disk('public')->url('videos/test.webm'),
        ]);

        Artisan::call('videos:convert-pending', ['--dry-run' => true]);

        Bus::assertNotDispatched(ConvertVideoToMp4::class);
    }

    public function test_command_skips_missing_files(): void
    {
        $event = Event::factory()->create();

        Video::factory()->for($event)->create([
            'title' => 'Missing File Video',
            'mime_type' => 'video/webm',
            'conversion_status' => 'pending',
            'video_url' => Storage::disk('public')->url('videos/missing.webm'),
        ]);

        Artisan::call('videos:convert-pending');

        Bus::assertNotDispatched(ConvertVideoToMp4::class);
    }

    public function test_command_handles_empty_database(): void
    {
        Artisan::call('videos:convert-pending');

        Bus::assertNotDispatched(ConvertVideoToMp4::class);
    }
}
