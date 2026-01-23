<?php

namespace Modules\PublicPage\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Tests\TestCase;

class TimelineVideoSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected $event;
    protected $videos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = Event::create([
            'name' => 'Test Event',
            'description' => 'Test Description',
            'icon' => '🎯',
            'category' => 'charity_event',
            'event_date' => now(),
            'slug' => 'test-event',
            'is_published' => TRUE,
        ]);

        Video::create([
            'event_id' => $this->event->id,
            'title' => 'Featured Video',
            'description' => 'Featured video description',
            'video_url' => 'https://example.com/featured.mp4',
            'thumbnail_url' => 'https://example.com/featured-thumb.jpg',
            'duration_seconds' => 765,
            'is_featured' => TRUE,
            'sort_order' => 1,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            Video::create([
                'event_id' => $this->event->id,
                'title' => 'Supporting Video ' . $i,
                'description' => 'Supporting video ' . $i . ' description',
                'video_url' => 'https://example.com/video-' . $i . '.mp4',
                'thumbnail_url' => 'https://example.com/video-' . $i . '-thumb.jpg',
                'duration_seconds' => 600 + ($i * 60),
                'is_featured' => FALSE,
                'sort_order' => $i + 1,
            ]);
        }

        $this->videos = $this->event->videos()->get();
    }

    public function test_featured_video_created(): void
    {
        $featured = $this->event->videos()->where('is_featured', TRUE)->first();
        $this->assertNotNull($featured);
        $this->assertEquals('Featured Video', $featured->title);
    }

    public function test_supporting_videos_created(): void
    {
        $supporting = $this->event->videos()->where('is_featured', FALSE)->get();
        $this->assertEquals(3, $supporting->count());
    }

    public function test_video_duration_formatted(): void
    {
        $video = $this->event->videos()->first();
        $this->assertIsString($video->formatDuration);
        $this->assertMatchesRegularExpression('/^\d{1,2}:\d{2}$/', $video->formatDuration);
    }

    public function test_featured_video_has_correct_duration(): void
    {
        $featured = $this->event->videos()->where('is_featured', TRUE)->first();
        $this->assertEquals('12:45', $featured->formatDuration);
    }

    public function test_event_has_featured_scope(): void
    {
        $featured = Video::featured()->get();
        $this->assertGreaterThanOrEqual(1, $featured->count());
        $featured->each(function ($video) {
            $this->assertTrue($video->is_featured);
        });
    }

    public function test_event_ordered_by_date(): void
    {
        Event::create([
            'name' => 'Earlier Event',
            'description' => 'Test',
            'icon' => '🎉',
            'category' => 'social_event',
            'event_date' => now()->subDays(5),
            'slug' => 'earlier-event',
            'is_published' => TRUE,
        ]);

        $events = Event::published()->ordered()->get();
        $this->assertGreaterThan(1, $events->count());
        $this->assertTrue($events->first()->event_date >= $events->last()->event_date);
    }

    public function test_event_published_scope(): void
    {
        Event::create([
            'name' => 'Unpublished Event',
            'description' => 'Test',
            'icon' => '🔒',
            'category' => 'charity_event',
            'event_date' => now(),
            'slug' => 'unpublished',
            'is_published' => FALSE,
        ]);

        $published = Event::published()->get();
        $total = Event::all();

        $this->assertEquals(1, $published->count());
        $this->assertEquals(2, $total->count());
    }
}
