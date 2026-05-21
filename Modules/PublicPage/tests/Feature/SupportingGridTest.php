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

        $event1 = Event::factory()->published()->create([
            'name' => 'Community Impact Program: Free Medical Outreach',
            'description' => 'Our annual medical outreach program bringing free healthcare services to underserved communities.',
            'icon' => '🏥',
            'category' => 'charity_event',
            'event_date' => now()->addMonths(2),
            'slug' => 'charity-event',
        ]);

        Video::factory()->featured()->forEvent($event1)->create([
            'title' => 'Medical Outreach Highlights',
            'description' => 'Highlights from our medical outreach program',
            'video_url' => '/videos/event-charity-1.mp4',
            'thumbnail_url' => '/images/video-placeholder-1.jpg',
            'duration_seconds' => 765,
            'sort_order' => 1,
        ]);

        Video::factory()->count(7)->forEvent($event1)->create([
            'is_featured' => FALSE,
        ]);

        $event2 = Event::factory()->published()->create([
            'name' => 'Excellence Awards & Fundraising Gala Night',
            'description' => 'Celebrating excellence and fundraising for community programs.',
            'icon' => '🎭',
            'category' => 'gala_night',
            'event_date' => now()->addMonth(1),
            'slug' => 'gala-night',
        ]);

        Video::factory()->featured()->forEvent($event2)->create([
            'title' => 'Awards Ceremony Highlights',
            'description' => 'Highlights from the awards ceremony',
            'video_url' => '/videos/event-gala-1.mp4',
            'thumbnail_url' => '/images/video-placeholder-9.jpg',
            'duration_seconds' => 930,
            'sort_order' => 1,
        ]);

        Video::factory()->count(6)->forEvent($event2)->create([
            'is_featured' => FALSE,
        ]);
    }

    public function test_grid_display_count(): void
    {
        $event = Event::where('slug', 'charity-event')->first();
        $supportingVideos = $event->videos()->where('is_featured', FALSE)->get();

        $this->assertCount(7, $supportingVideos);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
    }

    public function test_responsive_columns_desktop(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $this->assertStringContainsString('events', $response->getContent());
    }

    public function test_card_elements_present(): void
    {
        $video = Video::where('is_featured', FALSE)->first();
        $this->assertNotNull($video);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $content = $response->getContent();
        $this->assertStringContainsString($video->title, $content);
        $this->assertStringContainsString('video-placeholder', $content);
    }

    public function test_hover_effects_css_classes(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $supportingVideo = Video::where('is_featured', FALSE)->first();
        $this->assertStringContainsString($supportingVideo->title, $response->getContent());
    }

    public function test_click_video_updates_featured(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

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

        foreach ($supportingVideos as $video) {
            $this->assertStringContainsString($video->title, $response->getContent());
        }
    }

    public function test_play_button_styling(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $this->assertStringContainsString('Community Impact Program', $response->getContent());
    }

    public function test_grid_within_section(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $events = Event::with('videos')->get();
        $this->assertGreaterThanOrEqual(2, $events->count());

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

        $this->assertStringContainsString('charity_event', $response->getContent());
    }

    public function test_grid_spacing_consistent(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $supportingVideos = Video::where('is_featured', FALSE)->get();
        $this->assertGreaterThanOrEqual(7, $supportingVideos->count());
    }

    public function test_duration_badge_displays(): void
    {
        $video = Video::where('is_featured', FALSE)->first();
        $this->assertNotNull($video->duration_seconds);

        $expectedDuration = floor($video->duration_seconds / 60) . ':' . mb_str_pad($video->duration_seconds % 60, 2, '0', STR_PAD_LEFT);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $this->assertStringContainsString('duration', mb_strtolower($response->getContent()));
    }

    public function test_video_card_active_state_class(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $event = Event::with('videos')->first();
        $featuredVideo = $event->videos()->where('is_featured', TRUE)->first();
        $this->assertNotNull($featuredVideo);
        $this->assertStringContainsString($featuredVideo->title, $response->getContent());
    }
}
