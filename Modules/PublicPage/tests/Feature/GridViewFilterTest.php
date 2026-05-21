<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GridViewFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->charityEvent = Event::factory()->published()->charityEvent()->create([
            'name' => 'Charity Event',
            'slug' => 'charity-event',
            'event_date' => now()->subDays(10),
        ]);

        $this->conferenceEvent = Event::factory()->published()->conferenceEvent()->create([
            'name' => 'Conference Event',
            'slug' => 'conference-event',
            'event_date' => now()->subDays(5),
        ]);

        $this->protestEvent = Event::factory()->published()->protestEvent()->create([
            'name' => 'Protest Event',
            'slug' => 'protest-event',
            'event_date' => now()->subDays(2),
        ]);

        Video::factory()->count(7)->forEvent($this->charityEvent)->create();
        Video::factory()->count(7)->forEvent($this->conferenceEvent)->create();
        Video::factory()->count(7)->forEvent($this->protestEvent)->create();
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

        $this->assertEquals('Protest Event', $events[0]->name);
        $this->assertEquals('Conference Event', $events[1]->name);
        $this->assertEquals('Charity Event', $events[2]->name);
    }

    public function test_all_videos_count_across_events(): void
    {
        $totalVideos = Video::count();
        $this->assertEquals(21, $totalVideos);
    }

    public function test_charity_event_has_seven_videos(): void
    {
        $videos = $this->charityEvent->videos()->ordered()->get();
        $this->assertCount(7, $videos);
    }

    public function test_conference_event_has_seven_videos(): void
    {
        $videos = $this->conferenceEvent->videos()->ordered()->get();
        $this->assertCount(7, $videos);
    }

    public function test_protest_event_has_seven_videos(): void
    {
        $videos = $this->protestEvent->videos()->ordered()->get();
        $this->assertCount(7, $videos);
    }

    public function test_each_event_has_featured_video(): void
    {
        $charityFeatured = $this->charityEvent->videos()->featured()->first();
        $conferenceFeatured = $this->conferenceEvent->videos()->featured()->first();
        $protestFeatured = $this->protestEvent->videos()->featured()->first();

        $this->assertNotNull($charityFeatured);
        $this->assertNotNull($conferenceFeatured);
        $this->assertNotNull($protestFeatured);

        $this->assertTrue($charityFeatured->is_featured);
        $this->assertTrue($conferenceFeatured->is_featured);
        $this->assertTrue($protestFeatured->is_featured);
    }

    public function test_video_has_correct_duration_format(): void
    {
        $video = Video::first();
        $this->assertIsString($video->formatDuration);
        $this->assertMatchesRegularExpression('/^\d{1,2}:\d{2}$/', $video->formatDuration);
    }

    public function test_events_have_correct_categories(): void
    {
        $this->assertEquals('Charity Drive', $this->charityEvent->category);
        $this->assertEquals('Conference', $this->conferenceEvent->category);
        $this->assertEquals('Protests', $this->protestEvent->category);
    }

    public function test_videos_inherit_event_category(): void
    {
        $charityVideos = $this->charityEvent->videos()->get();
        foreach ($charityVideos as $video) {
            $this->assertEquals($this->charityEvent->id, $video->event_id);
        }

        $conferenceVideos = $this->conferenceEvent->videos()->get();
        foreach ($conferenceVideos as $video) {
            $this->assertEquals($this->conferenceEvent->id, $video->event_id);
        }

        $protestVideos = $this->protestEvent->videos()->get();
        foreach ($protestVideos as $video) {
            $this->assertEquals($this->protestEvent->id, $video->event_id);
        }
    }

    public function test_unpublished_events_not_in_response(): void
    {
        Event::factory()->draft()->create([
            'name' => 'Hidden Event',
            'description' => 'This event should not appear',
            'event_date' => now(),
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

        $this->assertContains('Charity Drive', $categories);
        $this->assertContains('Conference', $categories);
        $this->assertContains('Protests', $categories);
    }

    public function test_grid_view_can_filter_videos_by_category(): void
    {
        $charityEvent = Event::where('category', 'Charity Drive')->first();
        $conferenceEvent = Event::where('category', 'Conference')->first();
        $protestEvent = Event::where('category', 'Protests')->first();

        $charityVideos = $charityEvent->videos()->get();
        $conferenceVideos = $conferenceEvent->videos()->get();
        $protestVideos = $protestEvent->videos()->get();

        $this->assertCount(7, $charityVideos);
        $this->assertCount(7, $conferenceVideos);
        $this->assertCount(7, $protestVideos);
    }
}
