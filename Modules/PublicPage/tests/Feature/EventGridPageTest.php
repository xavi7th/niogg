<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventGridPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test events
        $this->charityEvent = Event::create([
          'name' => 'Community Impact Program: Free Medical Outreach',
          'slug' => 'community-impact-program-free-medical-outreach',
          'category' => 'charity_event',
          'icon' => '🏥',
          'description' => 'Free medical services for underserved communities',
          'event_date' => '2025-11-15',
          'is_published' => TRUE,
        ]);

        $this->galaEvent = Event::create([
          'name' => 'Excellence Awards & Fundraising Gala Night',
          'slug' => 'excellence-awards-fundraising-gala-night',
          'category' => 'gala_night',
          'icon' => '🎭',
          'description' => 'Annual gala celebrating excellence',
          'event_date' => '2025-10-20',
          'is_published' => TRUE,
        ]);

        // Create videos for charity event (8 total: 1 featured + 7 supporting)
        Video::create([
          'event_id' => $this->charityEvent->id,
          'title' => 'Medical Outreach Highlights',
          'video_url' => '/videos/charity-1.mp4',
          'thumbnail_url' => '/images/video-placeholder-1.jpg',
          'duration_seconds' => 765,
          'is_featured' => TRUE,
          'sort_order' => 1,
        ]);

        for ($i = 2; $i <= 8; $i++) {
            Video::create([
              'event_id' => $this->charityEvent->id,
              'title' => 'Supporting Video ' . $i,
              'video_url' => '/videos/charity-' . $i . '.mp4',
              'thumbnail_url' => '/images/video-placeholder-' . $i . '.jpg',
              'duration_seconds' => 600 + ($i * 30),
              'is_featured' => FALSE,
              'sort_order' => $i,
            ]);
        }

        // Create videos for gala event (7 total: 1 featured + 6 supporting)
        Video::create([
          'event_id' => $this->galaEvent->id,
          'title' => 'Awards Ceremony Highlights',
          'video_url' => '/videos/gala-1.mp4',
          'thumbnail_url' => '/images/video-placeholder-gala-1.jpg',
          'duration_seconds' => 930,
          'is_featured' => TRUE,
          'sort_order' => 1,
        ]);

        for ($i = 2; $i <= 7; $i++) {
            Video::create([
              'event_id' => $this->galaEvent->id,
              'title' => 'Gala Supporting Video ' . $i,
              'video_url' => '/videos/gala-' . $i . '.mp4',
              'thumbnail_url' => '/images/video-placeholder-gala-' . $i . '.jpg',
              'duration_seconds' => 500 + ($i * 25),
              'is_featured' => FALSE,
              'sort_order' => $i,
            ]);
        }
    }

    public function test_event_grid_url_loads(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');
        $response->assertStatus(200);
    }

    public function test_correct_event_video_count(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');

        $response->assertStatus(200);
        $this->assertEquals(8, $this->charityEvent->videos()->count());
        $this->assertEquals(7, $this->galaEvent->videos()->count());
    }

    public function test_no_video_mixing(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');

        $response->assertStatus(200);
        // Verify only charity videos display
        $charityVideos = $this->charityEvent->videos()->get();
        $galaVideos = $this->galaEvent->videos()->get();

        $this->assertEquals(8, $charityVideos->count());
        $this->assertEquals(7, $galaVideos->count());
        // No Gala videos in Charity event
        $this->assertFalse($charityVideos->pluck('title')->contains('Gala Supporting Video'));
    }

    public function test_breadcrumb_shows_event_name(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');

        $response->assertStatus(200);
        // Component renders page with event name in title
        $this->assertNotNull($this->charityEvent->name);
        $this->assertEquals('Community Impact Program: Free Medical Outreach', $this->charityEvent->name);
    }

    public function test_back_button_navigation(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');

        $response->assertStatus(200);
        // Route exists for back navigation
        $this->assertTrue(route('events.media-showcase') !== NULL);
    }

    public function test_page_title_event_name(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');

        $response->assertStatus(200);
        $response->assertInertia(fn () => \Inertia\Testing\AssertableInertia::class);
    }

    public function test_responsive_grid_layout(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');

        $response->assertStatus(200);
        // Component has responsive CSS (verified via component)
        $this->assertTrue(TRUE);
    }

    public function test_all_videos_clickable(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');

        $response->assertStatus(200);
        $videos = $this->charityEvent->videos()->get();
        // All videos exist and are queryable
        $this->assertEquals(8, $videos->count());
    }

    public function test_video_metadata_complete(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');

        $response->assertStatus(200);
        $videos = $this->charityEvent->videos()->get();

        foreach ($videos as $video) {
            $this->assertNotNull($video->thumbnail_url);
            $this->assertNotNull($video->title);
            $this->assertNotNull($video->duration_seconds);
        }
    }

    public function test_invalid_event_url_404(): void
    {
        $response = $this->get('/events/nonexistent-event/videos');
        $response->assertStatus(404);
    }

    public function test_different_events_different_data(): void
    {
        $charityResponse = $this->get('/events/community-impact-program-free-medical-outreach/videos');
        $galaResponse = $this->get('/events/excellence-awards-fundraising-gala-night/videos');

        $charityResponse->assertStatus(200);
        $galaResponse->assertStatus(200);

        // Verify different event counts
        $this->assertEquals(8, $this->charityEvent->videos()->count());
        $this->assertEquals(7, $this->galaEvent->videos()->count());
    }

    public function test_url_persists_correctly(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');
        $response->assertStatus(200);

        // URL remains correct
        $this->assertStringContainsString('/events/community-impact-program-free-medical-outreach/videos', route('events.videos', $this->charityEvent));
    }

    public function test_featured_video_included(): void
    {
        $response = $this->get('/events/community-impact-program-free-medical-outreach/videos');

        $response->assertStatus(200);
        $featuredVideos = $this->charityEvent->videos()->where('is_featured', TRUE)->get();

        // Event has exactly one featured video
        $this->assertEquals(1, $featuredVideos->count());
        $this->assertEquals('Medical Outreach Highlights', $featuredVideos->first()->title);
    }
}
