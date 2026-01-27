<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use ReflectionClass;
use InvalidArgumentException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Services\VideoThumbnailService;

class VideoThumbnailServiceTest extends TestCase
{
    use RefreshDatabase;

    private VideoThumbnailService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VideoThumbnailService;
        Storage::fake('public');
    }

    public function test_delete_thumbnails_removes_all_sizes(): void
    {
        $thumbnailUrl = Storage::disk('public')->url('videos/thumbnails/abc123_medium.jpg');

        // Create fake thumbnail files
        Storage::disk('public')->makeDirectory('videos/thumbnails');
        Storage::disk('public')->put('videos/thumbnails/abc123_small.jpg', 'fake-image');
        Storage::disk('public')->put('videos/thumbnails/abc123_medium.jpg', 'fake-image');
        Storage::disk('public')->put('videos/thumbnails/abc123_large.jpg', 'fake-image');

        $this->service->deleteThumbnails($thumbnailUrl);

        Storage::disk('public')->assertMissing('videos/thumbnails/abc123_small.jpg');
        Storage::disk('public')->assertMissing('videos/thumbnails/abc123_medium.jpg');
        Storage::disk('public')->assertMissing('videos/thumbnails/abc123_large.jpg');
    }

    public function test_delete_thumbnails_handles_empty_url(): void
    {
        // Should not throw exception
        $this->service->deleteThumbnails('');
        $this->assertTrue(TRUE);
    }

    public function test_delete_thumbnails_handles_null_url(): void
    {
        // Should not throw exception
        $this->service->deleteThumbnails(NULL);
        $this->assertTrue(TRUE);
    }

    public function test_get_all_sizes_returns_correct_urls(): void
    {
        $thumbnailUrl = Storage::disk('public')->url('videos/thumbnails/test123_medium.jpg');

        $sizes = $this->service->getAllSizes($thumbnailUrl);

        $this->assertArrayHasKey('small', $sizes);
        $this->assertArrayHasKey('medium', $sizes);
        $this->assertArrayHasKey('large', $sizes);

        $this->assertStringContainsString('test123_small.jpg', $sizes['small']);
        $this->assertStringContainsString('test123_medium.jpg', $sizes['medium']);
        $this->assertStringContainsString('test123_large.jpg', $sizes['large']);
    }

    public function test_get_all_sizes_handles_empty_url(): void
    {
        $sizes = $this->service->getAllSizes('');

        $this->assertNull($sizes['small']);
        $this->assertNull($sizes['medium']);
        $this->assertNull($sizes['large']);
    }

    public function test_get_all_sizes_handles_null_url(): void
    {
        $sizes = $this->service->getAllSizes(NULL);

        $this->assertNull($sizes['small']);
        $this->assertNull($sizes['medium']);
        $this->assertNull($sizes['large']);
    }

    public function test_generate_from_path_throws_exception_for_nonexistent_file(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Video file not found');

        $this->service->generateFromPath('videos/nonexistent.mp4', 10);
    }

    public function test_thumbnail_sizes_are_correctly_defined(): void
    {
        // Test the SIZE constants are accessible and correct
        $service = new ReflectionClass(VideoThumbnailService::class);
        $sizes = $service->getConstant('SIZES');

        $this->assertIsArray($sizes);
        $this->assertArrayHasKey('small', $sizes);
        $this->assertArrayHasKey('medium', $sizes);
        $this->assertArrayHasKey('large', $sizes);

        $this->assertEquals([320, 180], $sizes['small']);
        $this->assertEquals([640, 360], $sizes['medium']);
        $this->assertEquals([1280, 720], $sizes['large']);
    }
}
