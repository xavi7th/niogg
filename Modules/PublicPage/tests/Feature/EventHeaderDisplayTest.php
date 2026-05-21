<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventHeaderDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Event::factory()->published()->create([
            'name' => 'Community Impact Program: Free Medical Outreach',
            'description' => 'Our annual medical outreach program bringing free healthcare services to underserved communities.',
            'icon' => '🏥',
            'category' => 'charity_event',
            'event_date' => now()->addMonths(2),
            'slug' => 'charity-event',
        ]);

        Event::factory()->published()->create([
            'name' => 'Excellence Awards & Fundraising Gala Night',
            'description' => 'An evening of celebration recognizing outstanding contributions.',
            'icon' => '🎭',
            'category' => 'gala_night',
            'event_date' => now()->addMonth(),
            'slug' => 'gala-night',
        ]);
    }

    public function test_media_showcase_page_loads(): void
    {
        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
    }

    public function test_event_header_displays_title(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('Community Impact Program: Free Medical Outreach');
    }

    public function test_event_header_displays_category_badge(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('charity_event');
    }

    public function test_event_header_displays_description(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('Our annual medical outreach program bringing free healthcare services to underserved communities.');
    }

    public function test_all_event_headers_display(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('Community Impact Program: Free Medical Outreach');
        $response->assertSee('Excellence Awards & Fundraising Gala Night');
        $response->assertSee('charity_event');
        $response->assertSee('gala_night');
    }

    public function test_event_header_category_formatting(): void
    {
        $event = Event::where('slug', 'charity-event')->first();
        $this->assertNotNull($event);
        $this->assertEquals('charity_event', $event->category);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
        $response->assertSee('charity_event');
    }

    public function test_event_header_required_fields_exist(): void
    {
        $event = Event::where('slug', 'charity-event')->first();

        $this->assertNotNull($event->name);
        $this->assertNotNull($event->icon);
        $this->assertNotNull($event->category);
        $this->assertNotNull($event->description);

        $response = $this->get('/events/media-showcase');
        $response->assertStatus(200);
        $this->assertStringContainsString($event->name, $response->getContent());
        $this->assertStringContainsString($event->description, $response->getContent());
    }

    public function test_multiple_events_headers_ordered_by_date(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $content = $response->getContent();

        $charityPos = mb_strpos($content, 'Community Impact Program');
        $galaPos = mb_strpos($content, 'Excellence Awards');

        $this->assertNotFalse($charityPos);
        $this->assertNotFalse($galaPos);
        $this->assertLessThan($galaPos, $charityPos);
    }
}
