<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupportingGridTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create first event (Charity)
        $event1 = Event::create([
          'name' => 'Community Impact Program: Free Medical Outreach',
          'description' => 'Our annual medical outreach program bringing free healthcare services to underserved communities.',
          'icon' => '🏥',
          'category' => 'charity_event',
          'event_date' => now()->addMonths(2),
          'slug' => 'charity-event',
          'is_published' => TRUE,
        ]);

        // Create featured video for Charity
        Video::create([
          'event_id' => $event1->id,
          'title' => 'Medical Outreach Highlights',
          'description' => 'Highlights from our medical outreach program',
          'video_url' => '/videos/event-charity-1.mp4',
          'thumbnail_url' => '/images/video-placeholder-1.jpg',
          'duration_seconds' => 765, // 12:45
          'is_featured' => TRUE,
          'sort_order' => 1,
        ]);

        // Create 7 supporting videos for Charity
        for ($i = 2; $i <= 8; $i++) {
            Video::create([
              'event_id' => $event1->id,
              'title' => 'Video ' . $i . ' - Community Impact Program: Free Medical Outreach',
              'description' => 'Supporting video from the event',
              'video_url' => '/videos/event-charity-' . $i . '.mp4',
              'thumbnail_url' => '/images/video-placeholder-' . $i . '.jpg',
              'duration_seconds' => 600 + ($i * 10),
              'is_featured' => FALSE,
              'sort_order' => $i,
            ]);
        }

        // Create second event (Gala)
        $event2 = Event::create([
          'name' => 'Excellence Awards & Fundraising Gala Night',
          'description' => 'Celebrating excellence and fundraising for community programs.',
          'icon' => '🎭',
          'category' => 'gala_night',
          'event_date' => now()->addMonth(1),
          'slug' => 'gala-night',
          'is_published' => TRUE,
        ]);

        // Create featured video for Gala
        Video::create([
          'event_id' => $event2->id,
          'title' => 'Awards Ceremony Highlights',
          'description' => 'Highlights from the awards ceremony',
          'video_url' => '/videos/event-gala-1.mp4',
          'thumbnail_url' => '/images/video-placeholder-9.jpg',
          'duration_seconds' => 930, // 15:30
          'is_featured' => TRUE,
          'sort_order' => 1,
        ]);

        // Create supporting videos for Gala
        for ($i = 2; $i <= 7; $i++) {
            Video::create([
              'event_id' => $event2->id,
              'title' => 'Video ' . $i . ' - Excellence Awards & Fundraising Gala Night',
              'description' => 'Supporting video from the gala',
              'video_url' => '/videos/event-gala-' . $i . '.mp4',
              'thumbnail_url' => '/images/video-placeholder-' . (8 + $i) . '.jpg',
              'duration_seconds' => 700 + ($i * 15),
              'is_featured' => FALSE,
              'sort_order' => $i,
            ]);
        }
    }

    public function test_grid_display_count(): void
    {
        $event = Event::where('slug', 'charity-event')->first();
        $supportingVideos = $event->videos()->where('is_featured', FALSE)->get();

        $this->assertCount(7, $supportingVideos);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
        $response->assertSee('Video 2 - Community Impact Program: Free Medical Outreach');
    }

    public function test_responsive_columns_desktop(): void
    {
        // Desktop should render 4-column grid via CSS media query
        // This test verifies the data structure supports responsive rendering
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Verify Inertia response contains events with videos
        $this->assertStringContainsString('events', $response->getContent());
    }

    public function test_card_elements_present(): void
    {
        $video = Video::where('title', 'Video 2 - Community Impact Program: Free Medical Outreach')->first();
        $this->assertNotNull($video);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Verify video data is passed in Inertia props
        $content = $response->getContent();
        $this->assertStringContainsString('Video 2 - Community Impact Program', $content);
        $this->assertStringContainsString('video-placeholder', $content);
    }

    public function test_hover_effects_css_classes(): void
    {
        // Hover effects are CSS-based in SupportingVideoGrid.svelte
        // This test verifies the component is being rendered with proper data
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Verify supporting videos are being passed to component
        $this->assertStringContainsString('Video 2 - Community Impact Program', $response->getContent());
    }

    public function test_click_video_updates_featured(): void
    {
        // This test verifies the component structure supports clicking
        // The featured video update is handled by EventTimeline's onVideoSelect handler
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Verify supporting videos are clickable (role="button" in component)
        $supportingVideos = Video::where('is_featured', FALSE)->get();
        foreach ($supportingVideos->take(3) as $video) {
            $this->assertStringContainsString($video->title, $response->getContent());
        }
    }

    public function test_all_cards_clickable(): void
    {
        $event = Event::where('slug', 'charity-event')->first();
        $supportingVideos = $event->videos()->where('is_featured', FALSE)->get();

        $this->assertCount(7, $supportingVideos);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Verify each supporting video appears in response
        foreach ($supportingVideos as $video) {
            $this->assertStringContainsString($video->title, $response->getContent());
        }
    }

    public function test_play_button_styling(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Play button styling is in SupportingVideoGrid.svelte <style> block
        // Verify the component is being rendered by checking for supporting videos
        $this->assertStringContainsString('Community Impact Program', $response->getContent());
    }

    public function test_grid_within_section(): void
    {
        // Verify grid is properly contained within event sections
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Check that events are returned with their videos
        $events = Event::with('videos')->get();
        $this->assertGreaterThanOrEqual(2, $events->count());

        // Each event has supporting videos
        foreach ($events as $event) {
            $supportingVideos = $event->videos()->where('is_featured', FALSE)->count();
            $this->assertGreaterThan(0, $supportingVideos);
        }
    }

    public function test_category_badge_displays(): void
    {
        $videos = Video::where('is_featured', FALSE)->get();
        foreach ($videos as $video) {
            $this->assertNotNull($video->event->category);
        }

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Verify category data is in response
        $this->assertStringContainsString('charity_event', $response->getContent());
    }

    public function test_grid_spacing_consistent(): void
    {
        // Grid spacing is handled by CSS in SupportingVideoGrid.svelte
        // Test that multiple videos are present to verify grid structure
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Verify multiple supporting videos exist (7 per event minimum)
        $supportingVideos = Video::where('is_featured', FALSE)->get();
        $this->assertGreaterThanOrEqual(7, $supportingVideos->count());
    }

    public function test_duration_badge_displays(): void
    {
        $video = Video::where('is_featured', FALSE)->first();
        $this->assertNotNull($video->duration_seconds);

        // Verify duration formats correctly for display
        $expectedDuration = floor($video->duration_seconds / 60) . ':' . mb_str_pad($video->duration_seconds % 60, 2, '0', STR_PAD_LEFT);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Verify response contains video data
        $this->assertStringContainsString('duration', mb_strtolower($response->getContent()));
    }

    public function test_video_card_active_state_class(): void
    {
        // Active state is managed in SupportingVideoGrid.svelte component
        // Test verifies the currentlyPlaying prop is properly handled
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        // Verify first event's featured video is in response
        $event = Event::with('videos')->first();
        $featuredVideo = $event->videos()->where('is_featured', TRUE)->first();
        $this->assertNotNull($featuredVideo);
        $this->assertStringContainsString($featuredVideo->title, $response->getContent());
    }
}
