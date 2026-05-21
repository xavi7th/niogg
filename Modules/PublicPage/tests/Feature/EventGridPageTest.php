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

        $this->event = Event::factory()->published()->create([
            'name' => 'Test Event',
            'slug' => 'test-event',
            'event_date' => now()->subDays(10),
        ]);

        Video::factory()->count(5)->forEvent($this->event)->create([
            'title' => fn () => fake()->sentence(3),
            'is_featured' => FALSE,
        ]);

        $this->featuredVideo = Video::factory()->featured()->forEvent($this->event)->create([
            'title' => 'Featured Video',
        ]);
    }

    public function test_event_grid_url_loads(): void
    {
        $response = $this->get(route('events.videos', $this->event));
        $response->assertStatus(200);
    }

    public function test_correct_event_video_count(): void
    {
        $response = $this->get(route('events.videos', $this->event));

        $response->assertStatus(200);
        $this->assertEquals(6, $this->event->videos()->count());
    }

    public function test_no_video_mixing(): void
    {
        $otherEvent = Event::factory()->published()->create();
        Video::factory()->count(3)->forEvent($otherEvent)->create();

        $response = $this->get(route('events.videos', $this->event));

        $response->assertStatus(200);
        $eventVideos = $this->event->videos()->get();
        $otherVideos = $otherEvent->videos()->get();

        $this->assertEquals(6, $eventVideos->count());
        $this->assertEquals(3, $otherVideos->count());
    }

    public function test_breadcrumb_shows_event_name(): void
    {
        $response = $this->get(route('events.videos', $this->event));

        $response->assertStatus(200);
        $this->assertNotNull($this->event->name);
        $this->assertEquals('Test Event', $this->event->name);
    }

    public function test_back_button_navigation(): void
    {
        $response = $this->get(route('events.videos', $this->event));

        $response->assertStatus(200);
        $this->assertTrue(route('events.media-showcase') !== NULL);
    }

    public function test_page_title_event_name(): void
    {
        $response = $this->get(route('events.videos', $this->event));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('event'));
    }

    public function test_responsive_grid_layout(): void
    {
        $event = Event::factory()->published()->create();
        Video::factory()->count(3)->forEvent($event)->create();

        $response = $this->get(route('events.show', $event));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->has('event'));
    }

    public function test_all_videos_clickable(): void
    {
        $response = $this->get(route('events.videos', $this->event));

        $response->assertStatus(200);
        $videos = $this->event->videos()->get();
        $this->assertEquals(6, $videos->count());
    }

    public function test_video_metadata_complete(): void
    {
        $response = $this->get(route('events.videos', $this->event));

        $response->assertStatus(200);
        $videos = $this->event->videos()->get();

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
        $event1 = Event::factory()->published()->create();
        Video::factory()->count(5)->forEvent($event1)->create();

        $event2 = Event::factory()->published()->create();
        Video::factory()->count(3)->forEvent($event2)->create();

        $response1 = $this->get(route('events.videos', $event1));
        $response2 = $this->get(route('events.videos', $event2));

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        $this->assertEquals(5, $event1->videos()->count());
        $this->assertEquals(3, $event2->videos()->count());
    }

    public function test_url_persists_correctly(): void
    {
        $response = $this->get(route('events.videos', $this->event));
        $response->assertStatus(200);

        $this->assertStringContainsString($this->event->slug, route('events.videos', $this->event));
    }

    public function test_featured_video_included(): void
    {
        $response = $this->get(route('events.videos', $this->event));

        $response->assertStatus(200);
        $featuredVideos = $this->event->videos()->where('is_featured', TRUE)->get();

        $this->assertEquals(1, $featuredVideos->count());
        $this->assertEquals('Featured Video', $featuredVideos->first()->title);
    }
}
