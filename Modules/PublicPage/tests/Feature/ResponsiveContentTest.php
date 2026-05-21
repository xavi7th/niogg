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

    public function test_desktop_1920_layout(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase')
            ->assertSee('View All Videos');

        $response->assertSee('grid-template-columns: repeat(4, 1fr)');
    }

    public function test_tablet_768_layout(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase');

        $response->assertSee('@media (max-width: 991px)');
    }

    public function test_mobile_375_layout(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase')
            ->assertSee('View All Videos');

        $response->assertSee('@media (max-width: 768px)');
    }

    public function test_keyboard_accessibility_standards(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase');

        $response->assertSee('View All Videos');
    }

    public function test_responsive_grid_columns(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('@media (max-width: 991px)', $html);
        $this->assertStringContainsString('@media (max-width: 768px)', $html);
    }

    public function test_touch_target_sizes(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('min-height: 48px', $html);
        $this->assertStringContainsString('min-width: 48px', $html);
    }

    public function test_color_contrast_and_readability(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase');

        $response->assertSee('Community Impact Program');
        $response->assertSee('CHARITY EVENT');
    }

    public function test_image_alt_text_present(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('alt=', $html);
    }

    public function test_semantic_html_structure(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('<header', $html);
        $this->assertStringContainsString('<main', $html);
        $this->assertStringContainsString('<section', $html);
    }

    public function test_reduced_motion_support(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $html);
    }

    public function test_mobile_first_optimization(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('touch-action: pan-y', $html);
        $this->assertStringContainsString('-webkit-tap-highlight-color', $html);
    }

    public function test_performance_optimizations(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('will-change:', $html);
        $this->assertStringContainsString('transform: translateZ(0)', $html);
        $this->assertStringContainsString('content-visibility', $html);
    }

    public function test_accessibility_features(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('role="button"', $html);
        $this->assertStringContainsString('tabindex="0"', $html);
    }

    public function test_responsive_images(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('loading="lazy"', $html);
    }

    public function test_font_resizing(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('clamp(', $html);
    }

    public function test_layout_no_breakpoints(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        $html = $response->getContent();
        $this->assertStringContainsString('grid-template-columns', $html);
    }
}
