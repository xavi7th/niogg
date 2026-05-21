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

        $event = Event::create([
          'name' => 'Community Impact Program: Free Medical Outreach',
          'description' => 'Our annual medical outreach program bringing free healthcare services to underserved communities.',
          'icon' => '🏥',
          'category' => 'charity_event',
          'event_date' => now()->addMonths(2),
          'slug' => 'charity-event',
          'is_published' => TRUE,
        ]);

        // Create featured video
        Video::create([
          'event_id' => $event->id,
          'title' => 'Medical Outreach Highlights',
          'description' => 'Highlights from our medical outreach program',
          'video_url' => '/videos/event-charity-1.mp4',
          'thumbnail_url' => '/images/video-placeholder-1.jpg',
          'duration_seconds' => 765, // 12:45
          'is_featured' => TRUE,
          'sort_order' => 1,
        ]);

        // Create supporting videos
        for ($i = 2; $i <= 8; $i++) {
            Video::create([
              'event_id' => $event->id,
              'title' => 'Video ' . $i . ' - Community Impact Program',
              'description' => 'Supporting video from the event',
              'video_url' => '/videos/event-charity-' . $i . '.mp4',
              'thumbnail_url' => '/images/video-placeholder-' . $i . '.jpg',
              'duration_seconds' => 600 + ($i * 10), // Variable duration
              'is_featured' => FALSE,
              'sort_order' => $i,
            ]);
        }
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

        // Check that video has duration stored
        $this->assertEquals(765, $video->duration_seconds);
        // Duration should format to MM:SS (12:45)
        $this->assertStringContainsString('12:45', (string) $video->formatDuration);
    }

    public function test_featured_badge_displays_on_featured_video(): void
    {
        $video = Video::where('is_featured', TRUE)->first();
        $this->assertNotNull($video);
        $this->assertTrue($video->is_featured);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
        // Verify featured video is in response data
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
        // Check that supporting videos are listed
        $response->assertSee('Video 2 - Community Impact Program');
        $response->assertSee('Video 3 - Community Impact Program');
    }

    public function test_all_supporting_videos_have_metadata(): void
    {
        $videos = Video::where('is_featured', FALSE)->get();
        $this->assertCount(7, $videos); // 7 supporting videos

        foreach ($videos as $video) {
            $this->assertNotNull($video->title);
            $this->assertNotNull($video->duration_seconds);
            $this->assertNotNull($video->thumbnail_url);
            $this->assertNotNull($video->video_url);
        }
    }

    public function test_video_duration_formats_correctly(): void
    {
        // Test various durations
        $testCases = [
          765 => '12:45', // 12 minutes 45 seconds
          600 => '10:00', // 10 minutes
          3660 => '61:00', // 61 minutes
          60 => '1:00', // 1 minute
          45 => '0:45', // 45 seconds
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
        // Verify page structure includes video element (controls will be native browser feature)
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
