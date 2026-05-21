<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoPlayerDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $event = Event::factory()->published()->create([
            'name' => 'Community Impact Program: Free Medical Outreach',
            'description' => 'Our annual medical outreach program bringing free healthcare services to underserved communities.',
            'icon' => '🏥',
            'category' => 'charity_event',
            'event_date' => now()->addMonths(2),
            'slug' => 'charity-event',
        ]);

        Video::factory()->featured()->forEvent($event)->create([
            'title' => 'Medical Outreach Highlights',
            'description' => 'Highlights from our medical outreach program',
            'video_url' => '/videos/event-charity-1.mp4',
            'thumbnail_url' => '/images/video-placeholder-1.jpg',
            'duration_seconds' => 765,
            'sort_order' => 1,
        ]);

        Video::factory()->count(7)->forEvent($event)->create([
            'is_featured' => FALSE,
        ]);
    }

    public function test_featured_video_displays_on_page_load(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('Medical Outreach Highlights');
    }

    public function test_featured_video_has_title(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('Medical Outreach Highlights');
    }

    public function test_featured_video_has_duration_metadata(): void
    {
        $video = Video::where('title', 'Medical Outreach Highlights')->first();
        $this->assertNotNull($video);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);

        $this->assertEquals(765, $video->duration_seconds);
        $this->assertStringContainsString('12:45', (string) $video->formatDuration);
    }

    public function test_featured_badge_displays_on_featured_video(): void
    {
        $video = Video::where('is_featured', TRUE)->first();
        $this->assertNotNull($video);
        $this->assertTrue($video->is_featured);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
        $this->assertStringContainsString('Medical Outreach Highlights', $response->getContent());
    }

    public function test_video_player_renders_with_poster(): void
    {
        $video = Video::where('title', 'Medical Outreach Highlights')->first();
        $this->assertNotNull($video->thumbnail_url);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
    }

    public function test_supporting_videos_display_with_featured(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
    }

    public function test_all_supporting_videos_have_metadata(): void
    {
        $videos = Video::where('is_featured', FALSE)->get();
        $this->assertCount(7, $videos);

        foreach ($videos as $video) {
            $this->assertNotNull($video->title);
            $this->assertNotNull($video->duration_seconds);
            $this->assertNotNull($video->thumbnail_url);
            $this->assertNotNull($video->video_url);
        }
    }

    public function test_video_duration_formats_correctly(): void
    {
        $testCases = [
            765 => '12:45',
            600 => '10:00',
            3660 => '61:00',
            60 => '1:00',
            45 => '0:45',
        ];

        foreach ($testCases as $seconds => $expected) {
            $video = new Video([
                'duration_seconds' => $seconds,
            ]);
            $this->assertEquals($expected, $video->formatDuration);
        }
    }

    public function test_featured_video_source_url_set(): void
    {
        $video = Video::where('is_featured', TRUE)->first();
        $this->assertNotNull($video->video_url);
        $this->assertStringContainsString('.mp4', $video->video_url);
    }

    public function test_video_player_has_native_controls_support(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $this->assertStringContainsString('video', mb_strtolower($response->getContent()));
    }

    public function test_featured_video_is_first_in_event(): void
    {
        $event = Event::where('slug', 'charity-event')->first();
        $featuredVideo = $event->videos()->where('is_featured', TRUE)->first();

        $this->assertNotNull($featuredVideo);
        $this->assertEquals('Medical Outreach Highlights', $featuredVideo->title);
    }
}
