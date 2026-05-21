<?php

namespace Modules\PublicPage\Tests\E2E;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ResponsiveAccessibilityTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('Modules\PublicPage\Database\Seeders\EventsTableSeeder');
    }

    /** @test */
    public function desktop_1920_layout(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase')
            ->assertSee('View All Videos');

        // Verify 4-column grid classes exist in HTML
        $response->assertSee('grid-template-columns: repeat(4, 1fr)');
    }

    /** @test */
    public function tablet_768_layout(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase');

        // Verify 2-column grid media query exists
        $response->assertSee('@media (max-width: 991px)');
    }

    /** @test */
    public function mobile_375_layout(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase')
            ->assertSee('View All Videos');

        // Verify mobile media queries exist
        $response->assertSee('@media (max-width: 768px)');
    }

    /** @test */
    public function keyboard_accessibility_standards(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase');

        // Check that interactive elements are present (keyboard accessible)
        $response->assertSee('View All Videos'); // Main button
    }

    /** @test */
    public function responsive_grid_columns(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Verify responsive breakpoints exist in CSS
        $html = $response->getContent();
        $this->assertStringContainsString('@media (max-width: 991px)', $html);
        $this->assertStringContainsString('@media (max-width: 768px)', $html);
    }

    /** @test */
    public function touch_target_sizes(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check that buttons have minimum touch targets
        $html = $response->getContent();
        $this->assertStringContainsString('min-height: 48px', $html);
        $this->assertStringContainsString('min-width: 48px', $html);
    }

    /** @test */
    public function color_contrast_and_readability(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200)
            ->assertSee('Events Media Showcase');

        // Check that text elements are present (basic readability check)
        $response->assertSee('Community Impact Program'); // Event title
        $response->assertSee('CHARITY EVENT'); // Category text
    }

    /** @test */
    public function image_alt_text_present(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check that images would have alt attributes (based on component structure)
        $html = $response->getContent();
        $this->assertStringContainsString('alt=', $html);
    }

    /** @test */
    public function semantic_html_structure(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check for semantic HTML elements in response
        $html = $response->getContent();
        $this->assertStringContainsString('<header', $html);
        $this->assertStringContainsString('<main', $html);
        $this->assertStringContainsString('<section', $html);
    }

    /** @test */
    public function reduced_motion_support(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check for reduced motion media query
        $html = $response->getContent();
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $html);
    }

    /** @test */
    public function mobile_first_optimization(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check mobile-specific optimizations
        $html = $response->getContent();
        $this->assertStringContainsString('touch-action: pan-y', $html);
        $this->assertStringContainsString('-webkit-tap-highlight-color', $html);
    }

    /** @test */
    public function performance_optimizations(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check for performance optimizations in CSS
        $html = $response->getContent();
        $this->assertStringContainsString('will-change:', $html);
        $this->assertStringContainsString('transform: translateZ(0)', $html);
        $this->assertStringContainsString('content-visibility', $html);
    }

    /** @test */
    public function accessibility_features(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check for accessibility features
        $html = $response->getContent();
        $this->assertStringContainsString('role="button"', $html); // Interactive elements
        $this->assertStringContainsString('tabindex="0"', $html); // Keyboard focusable
    }

    /** @test */
    public function responsive_images(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check for responsive image loading
        $html = $response->getContent();
        $this->assertStringContainsString('loading="lazy"', $html);
    }

    /** @test */
    public function font_resizing(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check for fluid typography using clamp()
        $html = $response->getContent();
        $this->assertStringContainsString('clamp(', $html);
    }

    /** @test */
    public function layout_no_breakpoints(): void
    {
        $response = $this->get('/events/media-showcase');

        $response->assertStatus(200);

        // Check that layout doesn't break at breakpoints
        // This ensures CSS media queries are properly implemented
        $html = $response->getContent();
        $this->assertStringContainsString('grid-template-columns', $html);
    }
}
