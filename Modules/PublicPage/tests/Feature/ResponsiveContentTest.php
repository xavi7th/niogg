<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ResponsiveContentTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('Modules\PublicPage\Database\Seeders\EventsTableSeeder');
    }

    public function test_media_showcase_page_loads(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
    }

    public function test_media_showcase_has_inertia_component(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('PublicPage::EventsMediaShowcase');
    }

    public function test_media_showcase_has_page_title(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('Events Media Showcase');
    }

    public function test_media_showcase_has_events(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('Free Medical Outreach 2025');
        $response->assertSee('Excellence Awards & Fundraising Gala');
        $response->assertSee('Youth Leadership Summit 2025');
    }

    public function test_media_showcase_has_categories(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('Free Medicals');
        $response->assertSee('Awards');
        $response->assertSee('Education');
    }

    public function test_media_showcase_has_canonical_url(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('rel="canonical"', FALSE);
        $response->assertSee('/events/media-showcase');
    }

    public function test_media_showcase_has_open_graph_tags(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('og:title');
        $response->assertSee('og:description');
        $response->assertSee('og:image');
    }

    public function test_media_showcase_has_twitter_card_tags(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('twitter:card');
        $response->assertSee('twitter:title');
        $response->assertSee('twitter:description');
    }

    public function test_app_has_viewport_meta(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('name="viewport"', FALSE);
    }

    public function test_app_has_csrf_token(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('csrf-token');
    }

    public function test_app_has_theme_color(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('theme-color');
    }

    public function test_app_has_favicon(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('favicon.png');
    }

    public function test_app_loads_js_bundle(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);
        $response->assertSee('/build/assets/app.js');
    }

    public function test_events_page_url_accessible(): void
    {
        $events = \Modules\PublicPage\Models\Event::all();

        foreach ($events as $event) {
            $response = $this->get('/events/' . $event->slug . '/videos');
            $response->assertStatus(200);
        }
    }
}
