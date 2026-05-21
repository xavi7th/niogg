<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimelineVideoSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->event = Event::factory()->published()->create([
            'name' => 'Test Event',
            'description' => 'Test Description',
            'icon' => '🎯',
            'category' => 'charity_event',
            'event_date' => now(),
            'slug' => 'test-event',
        ]);

        Video::factory()->featured()->forEvent($this->event)->create([
            'title' => 'Featured Video',
            'description' => 'Featured video description',
            'video_url' => 'https://example.com/featured.mp4',
            'thumbnail_url' => 'https://example.com/featured-thumb.jpg',
            'duration_seconds' => 765,
            'sort_order' => 1,
        ]);

        Video::factory()->count(3)->forEvent($this->event)->create([
            'is_featured' => FALSE,
        ]);

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
        $this->assertMatchesRegularExpression('/^\d+:\d{2}$/', $video->formatDuration);
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
        $featured->each(function ($video): void {
            $this->assertTrue($video->is_featured);
        });
    }

    public function test_event_ordered_by_date(): void
    {
        Event::factory()->published()->create([
            'name' => 'Earlier Event',
            'description' => 'Test',
            'icon' => '🎉',
            'category' => 'social_event',
            'event_date' => now()->subDays(5),
            'slug' => 'earlier-event',
        ]);

        $events = Event::published()->ordered()->get();
        $this->assertGreaterThan(1, $events->count());
        $this->assertTrue($events->first()->event_date >= $events->last()->event_date);
    }

    public function test_event_published_scope(): void
    {
        Event::factory()->draft()->create([
            'name' => 'Unpublished Event',
            'description' => 'Test',
            'icon' => '🔒',
            'category' => 'charity_event',
            'event_date' => now(),
            'slug' => 'unpublished',
        ]);

        $published = Event::published()->get();
        $total = Event::all();

        $this->assertEquals(1, $published->count());
        $this->assertEquals(2, $total->count());
    }
}
