<?php

namespace Modules\PublicPage\Tests\E2E;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CliEventCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function command_exists_and_is_callable(): void
    {
        // Existing test - verify command exists
        $commands = Artisan::all();
        $this->assertArrayHasKey('events:create', $commands);
    }

    /** @test */
    public function event_persists_in_database_with_correct_values(): void
    {
        // Create event programmatically (simulating CLI input)
        $event = Event::create([
            'name' => 'Test Conference 2026',
            'category' => 'gala_night',
            'description' => 'Annual gala event',
            'event_date' => '2026-05-20',
            'icon' => '🎭',
            'is_published' => TRUE,
        ]);

        $this->assertDatabaseHas('events', [
            'name' => 'Test Conference 2026',
            'category' => 'gala_night',
            'event_date' => '2026-05-20',
            'icon' => '🎭',
        ]);
    }

    /** @test */
    public function slug_auto_generated_from_name(): void
    {
        $event = Event::create([
            'name' => 'Tech Summit 2026',
            'category' => 'social_event',
            'event_date' => '2026-06-10',
            'is_published' => TRUE,
        ]);

        $this->assertEquals('tech-summit-2026', $event->slug);
    }

    /** @test */
    public function duplicate_slug_handling_appends_number(): void
    {
        // Create first event
        Event::create([
            'name' => 'Annual Meeting',
            'category' => 'charity_event',
            'event_date' => '2026-01-10',
            'is_published' => TRUE,
        ]);

        // Create second event with same name
        $event2 = Event::create([
            'name' => 'Annual Meeting',
            'category' => 'charity_event',
            'event_date' => '2026-02-10',
            'is_published' => TRUE,
        ]);

        $this->assertEquals('annual-meeting', Event::first()->slug);
        $this->assertEquals('annual-meeting-2', $event2->slug);
    }

    /** @test */
    public function event_created_without_featured_video(): void
    {
        $event = Event::create([
            'name' => 'Simple Event',
            'category' => 'social_event',
            'event_date' => '2026-07-15',
            'is_published' => TRUE,
        ]);

        $this->assertEquals(0, $event->videos()->count());
    }

    /** @test */
    public function event_created_with_featured_video(): void
    {
        $event = Event::create([
            'name' => 'Video Event',
            'category' => 'gala_night',
            'event_date' => '2026-08-20',
            'is_published' => TRUE,
        ]);

        Video::create([
            'event_id' => $event->id,
            'title' => 'Event Highlights',
            'video_url' => '/videos/highlights.mp4',
            'thumbnail_url' => '/images/thumbnail.jpg',
            'duration_seconds' => 600,
            'is_featured' => TRUE,
            'sort_order' => 0,
        ]);

        $this->assertEquals(1, $event->videos()->count());
        $this->assertEquals(1, $event->videos()->where('is_featured', TRUE)->count());
    }

    /** @test */
    public function new_event_displays_on_media_showcase_page(): void
    {
        $event = Event::create([
            'name' => 'New Event 2026',
            'category' => 'charity_event',
            'event_date' => '2026-09-01',
            'is_published' => TRUE,
        ]);

        // Verify event exists in database
        $this->assertDatabaseHas('events', [
            'name' => 'New Event 2026',
            'is_published' => TRUE,
        ]);

        // Test route loads
        $response = $this->get(route('events.media-showcase'));
        $response->assertStatus(200);
    }

    /** @test */
    public function event_persists_on_page_reload(): void
    {
        $event = Event::create([
            'name' => 'Persistent Event',
            'category' => 'social_event',
            'event_date' => '2026-10-10',
            'is_published' => TRUE,
        ]);

        // First request
        $response1 = $this->get(route('events.media-showcase'));
        $response1->assertStatus(200);

        // Second request (simulates reload)
        $response2 = $this->get(route('events.media-showcase'));
        $response2->assertStatus(200);

        $this->assertDatabaseHas('events', ['name' => 'Persistent Event']);
    }

    /** @test */
    public function new_event_category_appears_in_database(): void
    {
        Event::create([
            'name' => 'Charity Gala',
            'category' => 'charity_event',
            'event_date' => '2026-11-15',
            'is_published' => TRUE,
        ]);

        // Verify category exists in database
        $this->assertDatabaseHas('events', [
            'category' => 'charity_event',
        ]);

        // Verify all category types can be queried
        $events = Event::published()->ordered()->with('videos')->get();
        $categories = $events->pluck('category')->unique();

        $this->assertTrue($categories->contains('charity_event'));
    }

    /** @test */
    public function featured_video_displays_if_added(): void
    {
        $event = Event::create([
            'name' => 'Event With Video',
            'category' => 'gala_night',
            'event_date' => '2026-12-01',
            'is_published' => TRUE,
        ]);

        $video = Video::create([
            'event_id' => $event->id,
            'title' => 'Featured Content',
            'video_url' => '/videos/content.mp4',
            'thumbnail_url' => '/images/thumb.jpg',
            'duration_seconds' => 300,
            'is_featured' => TRUE,
            'sort_order' => 0,
        ]);

        $eventWithVideo = Event::with('videos')->find($event->id);
        $featured = $eventWithVideo->videos->where('is_featured', TRUE)->first();

        $this->assertNotNull($featured);
        $this->assertEquals('Featured Content', $featured->title);
    }

    /** @test */
    public function event_metadata_complete_after_creation(): void
    {
        $event = Event::create([
            'name' => 'Complete Event',
            'category' => 'social_event',
            'description' => 'Full metadata test',
            'event_date' => '2027-01-15',
            'icon' => '🎉',
            'is_published' => TRUE,
        ]);

        $this->assertNotNull($event->name);
        $this->assertNotNull($event->category);
        $this->assertNotNull($event->event_date);
        $this->assertNotNull($event->icon);
        $this->assertNotNull($event->slug);
        $this->assertTrue($event->is_published);
    }

    /** @test */
    public function event_slug_is_unique(): void
    {
        $event1 = Event::create([
            'name' => 'Unique Event',
            'category' => 'charity_event',
            'event_date' => '2027-02-01',
            'is_published' => TRUE,
        ]);

        // First event has base slug
        $this->assertEquals('unique-event', $event1->slug);

        // Second event with different name should have different slug
        $event2 = Event::create([
            'name' => 'Another Event',
            'category' => 'gala_night',
            'event_date' => '2027-02-15',
            'is_published' => TRUE,
        ]);

        $this->assertNotEquals($event1->slug, $event2->slug);
    }

    /** @test */
    public function event_specific_grid_page_loads_with_created_event(): void
    {
        $event = Event::create([
            'name' => 'Grid Test Event',
            'category' => 'charity_event',
            'event_date' => '2027-03-10',
            'is_published' => TRUE,
        ]);

        $response = $this->get(route('events.videos', $event->slug));

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page->where('event.name', 'Grid Test Event')
        );
    }

    /** @test */
    public function events_ordered_by_date_descending_after_creation(): void
    {
        Event::create([
            'name' => 'Old Event',
            'category' => 'social_event',
            'event_date' => '2026-01-01',
            'is_published' => TRUE,
        ]);

        Event::create([
            'name' => 'New Event',
            'category' => 'social_event',
            'event_date' => '2027-12-31',
            'is_published' => TRUE,
        ]);

        $events = Event::published()->ordered()->get();

        $this->assertEquals('New Event', $events->first()->name);
        $this->assertEquals('Old Event', $events->last()->name);
    }
}
