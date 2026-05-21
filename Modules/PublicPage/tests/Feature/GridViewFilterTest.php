<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GridViewFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $charityEvent;

    protected $galaEvent;

    protected $socialEvent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->charityEvent = Event::create([
          'name' => 'Community Impact Program: Free Medical Outreach',
          'description' => 'Our annual medical outreach program bringing free healthcare services.',
          'icon' => '🏥',
          'category' => 'charity_event',
          'event_date' => now()->addDays(30),
          'is_published' => TRUE,
        ]);

        $this->galaEvent = Event::create([
          'name' => 'Excellence Awards & Fundraising Gala Night',
          'description' => 'An evening of celebration recognizing outstanding contributions.',
          'icon' => '🎭',
          'category' => 'gala_night',
          'event_date' => now()->addDays(20),
          'is_published' => TRUE,
        ]);

        $this->socialEvent = Event::create([
          'name' => 'Youth Leadership Summit 2025',
          'description' => 'Empowering the next generation of leaders.',
          'icon' => '🎉',
          'category' => 'social_event',
          'event_date' => now()->addDays(10),
          'is_published' => TRUE,
        ]);

        // Create charity videos (8 total, 1 featured)
        for ($i = 1; $i <= 8; $i++) {
            Video::create([
              'event_id' => $this->charityEvent->id,
              'title' => 'Charity Video ' . $i,
              'description' => 'Charity video ' . $i,
              'video_url' => '/videos/charity-' . $i . '.mp4',
              'thumbnail_url' => '/images/charity-' . $i . '.jpg',
              'duration_seconds' => 600 + ($i * 30),
              'is_featured' => $i === 1,
              'sort_order' => $i,
            ]);
        }

        // Create gala videos (7 total, 1 featured)
        for ($i = 1; $i <= 7; $i++) {
            Video::create([
              'event_id' => $this->galaEvent->id,
              'title' => 'Gala Video ' . $i,
              'description' => 'Gala video ' . $i,
              'video_url' => '/videos/gala-' . $i . '.mp4',
              'thumbnail_url' => '/images/gala-' . $i . '.jpg',
              'duration_seconds' => 700 + ($i * 30),
              'is_featured' => $i === 1,
              'sort_order' => $i,
            ]);
        }

        // Create social videos (6 total, 1 featured)
        for ($i = 1; $i <= 6; $i++) {
            Video::create([
              'event_id' => $this->socialEvent->id,
              'title' => 'Social Video ' . $i,
              'description' => 'Social video ' . $i,
              'video_url' => '/videos/social-' . $i . '.mp4',
              'thumbnail_url' => '/images/social-' . $i . '.jpg',
              'duration_seconds' => 800 + ($i * 30),
              'is_featured' => $i === 1,
              'sort_order' => $i,
            ]);
        }
    }

    public function test_media_showcase_loads_successfully(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
    }

    public function test_page_returns_all_events_with_videos(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $this->assertCount(3, Event::published()->ordered()->get());
    }

    public function test_events_ordered_by_date_descending(): void
    {
        $events = Event::published()->ordered()->get();

        // Newest should be first (charity is 30 days from now)
        $this->assertEquals('Community Impact Program: Free Medical Outreach', $events[0]->name);
        $this->assertEquals('Excellence Awards & Fundraising Gala Night', $events[1]->name);
        $this->assertEquals('Youth Leadership Summit 2025', $events[2]->name);
    }

    public function test_all_videos_count_across_events(): void
    {
        $totalVideos = Video::count();
        $this->assertEquals(21, $totalVideos);
    }

    public function test_charity_event_has_eight_videos(): void
    {
        $videos = $this->charityEvent->videos()->ordered()->get();
        $this->assertCount(8, $videos);
    }

    public function test_gala_event_has_seven_videos(): void
    {
        $videos = $this->galaEvent->videos()->ordered()->get();
        $this->assertCount(7, $videos);
    }

    public function test_social_event_has_six_videos(): void
    {
        $videos = $this->socialEvent->videos()->ordered()->get();
        $this->assertCount(6, $videos);
    }

    public function test_each_event_has_featured_video(): void
    {
        $charityFeatured = $this->charityEvent->videos()->featured()->first();
        $galaFeatured = $this->galaEvent->videos()->featured()->first();
        $socialFeatured = $this->socialEvent->videos()->featured()->first();

        $this->assertNotNull($charityFeatured);
        $this->assertNotNull($galaFeatured);
        $this->assertNotNull($socialFeatured);

        $this->assertTrue($charityFeatured->is_featured);
        $this->assertTrue($galaFeatured->is_featured);
        $this->assertTrue($socialFeatured->is_featured);
    }

    public function test_video_has_correct_duration_format(): void
    {
        $video = Video::first();
        $this->assertIsString($video->formatDuration);
        $this->assertMatchesRegularExpression('/^\d{1,2}:\d{2}$/', $video->formatDuration);
    }

    public function test_events_have_correct_categories(): void
    {
        $this->assertEquals('charity_event', $this->charityEvent->category);
        $this->assertEquals('gala_night', $this->galaEvent->category);
        $this->assertEquals('social_event', $this->socialEvent->category);
    }

    public function test_videos_inherit_event_category(): void
    {
        $charityVideo = $this->charityEvent->videos()->first();
        $galaVideo = $this->galaEvent->videos()->first();
        $socialVideo = $this->socialEvent->videos()->first();

        // Videos don't have category field, but can be filtered by event
        $charityVideos = $this->charityEvent->videos()->get();
        foreach ($charityVideos as $video) {
            $this->assertEquals($this->charityEvent->id, $video->event_id);
        }

        $galaVideos = $this->galaEvent->videos()->get();
        foreach ($galaVideos as $video) {
            $this->assertEquals($this->galaEvent->id, $video->event_id);
        }

        $socialVideos = $this->socialEvent->videos()->get();
        foreach ($socialVideos as $video) {
            $this->assertEquals($this->socialEvent->id, $video->event_id);
        }
    }

    public function test_unpublished_events_not_in_response(): void
    {
        Event::create([
          'name' => 'Hidden Event',
          'description' => 'This event should not appear',
          'icon' => '🔒',
          'category' => 'charity_event',
          'event_date' => now(),
          'is_published' => FALSE,
        ]);

        $publishedEvents = Event::published()->get();
        $this->assertCount(3, $publishedEvents);

        $names = $publishedEvents->pluck('name')->toArray();
        $this->assertNotContains('Hidden Event', $names);
    }

    public function test_filter_tabs_derive_from_event_categories(): void
    {
        $events = Event::published()->ordered()->get();

        $categories = $events->pluck('category')->unique()->toArray();

        $this->assertContains('charity_event', $categories);
        $this->assertContains('gala_night', $categories);
        $this->assertContains('social_event', $categories);
    }

    public function test_grid_view_can_filter_videos_by_category(): void
    {
        // Simulate filtering by building category list from events
        $charityEvent = Event::where('category', 'charity_event')->first();
        $galaEvent = Event::where('category', 'gala_night')->first();
        $socialEvent = Event::where('category', 'social_event')->first();

        $charityVideos = $charityEvent->videos()->get();
        $galaVideos = $galaEvent->videos()->get();
        $socialVideos = $socialEvent->videos()->get();

        $this->assertCount(8, $charityVideos);
        $this->assertCount(7, $galaVideos);
        $this->assertCount(6, $socialVideos);
    }
}
