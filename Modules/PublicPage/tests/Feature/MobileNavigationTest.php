<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MobileNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->charityEvent = Event::factory()->published()->create([
            'name' => 'Community Impact Program: Free Medical Outreach',
            'description' => 'Our annual medical outreach program bringing free healthcare services.',
            'icon' => '🏥',
            'category' => 'charity_event',
            'event_date' => now()->addDays(30),
        ]);

        $this->galaEvent = Event::factory()->published()->create([
            'name' => 'Excellence Awards & Fundraising Gala Night',
            'description' => 'An evening of celebration recognizing outstanding contributions.',
            'icon' => '🎭',
            'category' => 'gala_night',
            'event_date' => now()->addDays(20),
        ]);

        $this->socialEvent = Event::factory()->published()->create([
            'name' => 'Youth Leadership Summit 2025',
            'description' => 'Empowering the next generation of leaders.',
            'icon' => '🎉',
            'category' => 'social_event',
            'event_date' => now()->addDays(10),
        ]);

        Video::factory()->featured()->forEvent($this->charityEvent)->create([
            'title' => 'Medical Outreach Highlights',
            'video_url' => '/videos/charity-1.mp4',
            'thumbnail_url' => '/images/video-1.jpg',
            'duration_seconds' => 765,
            'sort_order' => 1,
        ]);

        Video::factory()->featured()->forEvent($this->galaEvent)->create([
            'title' => 'Awards Ceremony Highlights',
            'video_url' => '/videos/gala-1.mp4',
            'thumbnail_url' => '/images/video-2.jpg',
            'duration_seconds' => 930,
            'sort_order' => 1,
        ]);

        Video::factory()->featured()->forEvent($this->socialEvent)->create([
            'title' => 'Youth Leadership Summit 2025 - Featured',
            'video_url' => '/videos/social-1.mp4',
            'thumbnail_url' => '/images/video-3.jpg',
            'duration_seconds' => 600,
            'sort_order' => 1,
        ]);

        Video::factory()->count(7)->forEvent($this->charityEvent)->create([
            'is_featured' => FALSE,
        ]);

        Video::factory()->count(6)->forEvent($this->galaEvent)->create([
            'is_featured' => FALSE,
        ]);

        Video::factory()->count(5)->forEvent($this->socialEvent)->create([
            'is_featured' => FALSE,
        ]);
    }

    public function test_media_showcase_route_200(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
    }

    public function test_charity_event_grid_route_200(): void
    {
        $response = $this->get('/events/' . $this->charityEvent->slug . '/videos');
        $response->assertStatus(200);
    }

    public function test_gala_event_grid_route_200(): void
    {
        $response = $this->get('/events/' . $this->galaEvent->slug . '/videos');
        $response->assertStatus(200);
    }

    public function test_social_event_grid_route_200(): void
    {
        $response = $this->get('/events/' . $this->socialEvent->slug . '/videos');
        $response->assertStatus(200);
    }

    public function test_charity_event_has_8_videos(): void
    {
        $count = $this->charityEvent->videos()->count();
        $this->assertEquals(8, $count);
    }

    public function test_gala_event_has_7_videos(): void
    {
        $count = $this->galaEvent->videos()->count();
        $this->assertEquals(7, $count);
    }

    public function test_social_event_has_6_videos(): void
    {
        $count = $this->socialEvent->videos()->count();
        $this->assertEquals(6, $count);
    }

    public function test_each_event_has_one_featured_video(): void
    {
        $charityFeatured = $this->charityEvent->videos()->where('is_featured', TRUE)->count();
        $galaFeatured = $this->galaEvent->videos()->where('is_featured', TRUE)->count();
        $socialFeatured = $this->socialEvent->videos()->where('is_featured', TRUE)->count();

        $this->assertEquals(1, $charityFeatured);
        $this->assertEquals(1, $galaFeatured);
        $this->assertEquals(1, $socialFeatured);
    }

    public function test_invalid_event_slug_returns_404(): void
    {
        $response = $this->get('/events/nonexistent-event/videos');
        $response->assertStatus(404);
    }

    public function test_published_events_count_is_3(): void
    {
        $count = Event::published()->count();
        $this->assertEquals(3, $count);
    }

    public function test_unpublished_event_not_returned(): void
    {
        Event::factory()->draft()->create([
            'name' => 'Unpublished',
            'description' => 'Test',
            'icon' => '❌',
            'category' => 'other',
            'event_date' => now(),
        ]);

        $count = Event::published()->count();
        $this->assertEquals(3, $count);
    }

    public function test_events_ordered_by_date_descending(): void
    {
        $events = Event::published()->ordered()->get();
        $this->assertEquals($this->charityEvent->id, $events[0]->id);
        $this->assertEquals($this->galaEvent->id, $events[1]->id);
        $this->assertEquals($this->socialEvent->id, $events[2]->id);
    }
}
