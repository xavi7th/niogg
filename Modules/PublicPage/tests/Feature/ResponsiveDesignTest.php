<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResponsiveDesignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('Modules\PublicPage\Database\Seeders\EventsTableSeeder');
    }

    /** @test */
    public function page_loads_with_seeded_events(): void
    {
        $response = $this->get('/events/media-showcase')
            ->assertOk()
            ->assertSee('Events Media Showcase');

        // Verify events are loaded
        $events = Event::published()->ordered()->get();
        $this->assertCount(3, $events);

        // Verify each event is rendered
        foreach ($events as $event) {
            $response->assertSee($event->name);
        }
    }

    /** @test */
    public function navigation_responds_to_different_routes(): void
    {
        // Test main media showcase
        $this->get('/events/media-showcase')
            ->assertOk()
            ->assertSee('Events Media Showcase');

        // Test event-specific page
        $event = Event::first();
        $this->get('/events/' . $event->slug . '/videos')
            ->assertOk()
            ->assertSee($event->name);
    }

    /** @test */
    public function events_have_proper_data_for_responsive_display(): void
    {
        $events = Event::published()->ordered()->get();

        foreach ($events as $event) {
            $this->assertNotEmpty($event->name);
            $this->assertNotEmpty($event->slug);
            $this->assertNotEmpty($event->category);
            $this->assertNotEmpty($event->icon);
            $this->assertTrue($event->is_published);
        }
    }

    /** @test */
    public function seo_attributes_are_present(): void
    {
        $response = $this->get('/events/media-showcase')
            ->assertOk();

        // Verify basic SEO meta tags are present
        $this->assertStringContainsString('viewport', $response->content());
        $this->assertStringContainsString('charset="utf-8"', $response->content());
    }

    /** @test */
    public function events_have_proper_date_formatting(): void
    {
        $events = Event::published()->ordered()->get();

        foreach ($events as $event) {
            // Verify dates are properly formatted
            $this->assertNotEmpty($event->event_date);
            $this->assertStringStartsWith('2025', $event->event_date);
        }
    }
}
