<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NavigationCriticalPathTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    // Run seeders
    $this->artisan('db:seed', [
      '--class' => 'Modules\PublicPage\Database\Seeders\EventsTableSeeder',
    ]);
    $this->artisan('db:seed', [
      '--class' => 'Modules\PublicPage\Database\Seeders\VideosTableSeeder',
    ]);
  }

  public function test_media_showcase_route_200(): void
  {
    $response = $this->get('/events/media-showcase');
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function test_event_grid_route_200(): void
  {
    $event = Event::published()->first();
    $this->assertNotNull($event);
    $response = $this->get('/events/' . $event->slug . '/videos');
    $this->assertEquals(200, $response->getStatusCode());
  }

  public function test_media_showcase_returns_inertia_response(): void
  {
    $response = $this->get('/events/media-showcase');
    $response->assertInertia(
        fn ($page) => $page
            ->has('events')
            ->has('pageTitle')
    );
  }

  public function test_event_grid_returns_inertia_response(): void
  {
    $event = Event::published()->first();
    $response = $this->get('/events/' . $event->slug . '/videos');
    $response->assertInertia(
        fn ($page) => $page
            ->has('event')
            ->has('videos')
            ->has('pageTitle')
    );
  }

  public function test_all_routes_accessible(): void
  {
    // Test main showcase route
    $response = $this->get('/events/media-showcase');
    $this->assertEquals(200, $response->getStatusCode());

    // Test each event-specific grid
    Event::published()->each(function ($event): void {
      $response = $this->get('/events/' . $event->slug . '/videos');
      $this->assertEquals(200, $response->getStatusCode(), 'Route failed for event: ' . $event->slug);
    });
  }

  public function test_invalid_routes_404(): void
  {
    $response = $this->get('/events/media-showcasetypo');
    $this->assertEquals(404, $response->getStatusCode());

    $response = $this->get('/events/nonexistent-event/videos');
    $this->assertEquals(404, $response->getStatusCode());
  }

  public function test_event_specific_routes_use_slug(): void
  {
    $event = Event::published()->first();

    // Verify slug is auto-generated correctly
    $this->assertNotEmpty($event->slug);

    // Verify route uses slug parameter
    $response = $this->get('/events/' . $event->slug . '/videos');
    $this->assertEquals(200, $response->getStatusCode());

    // Verify incorrect slug returns 404
    $response = $this->get('/events/wrong-slug/videos');
    $this->assertEquals(404, $response->getStatusCode());
  }

  public function test_multiple_events_have_unique_routes(): void
  {
    $events = Event::published()->get();
    $this->assertGreaterThanOrEqual(1, $events->count());

    $slugs = $events->pluck('slug')->toArray();

    // Verify all slugs are unique
    $this->assertEquals(count($slugs), count(array_unique($slugs)));

    // Verify each route loads
    foreach ($slugs as $slug) {
      $response = $this->get('/events/' . $slug . '/videos');
      $this->assertEquals(200, $response->getStatusCode());
    }
  }

  public function test_correct_event_data_returned_per_route(): void
  {
    $event = Event::published()->first();
    $response = $this->get('/events/' . $event->slug . '/videos');

    $response->assertInertia(
        fn ($page) => $page
            ->where('event.id', $event->id)
    );
  }

  public function test_media_showcase_shows_all_events(): void
  {
    $response = $this->get('/events/media-showcase');

    $response->assertInertia(
        fn ($page) => $page
            ->has('events', 3)
    );
  }

  public function test_routes_ordered_correctly(): void
  {
    $response = $this->get('/events/media-showcase');

    $response->assertInertia(
        fn ($page) => $page
            ->where('events.0.name', 'Free Medical Outreach 2025')
    );
  }

  public function test_unpublished_events_excluded_from_media_showcase(): void
  {
    $response = $this->get('/events/media-showcase');

    $response->assertInertia(
        fn ($page) => $page
            ->has('events', 3)
    );

    // Create unpublished event
    Event::create([
      'name' => 'Unpublished Event',
      'description' => 'Should not appear',
      'icon' => '🔒',
      'category' => 'Conference',
      'event_date' => now(),
      'is_published' => FALSE,
    ]);

    // Verify it's still not in the response
    $response = $this->get('/events/media-showcase');
    $response->assertInertia(
        fn ($page) => $page
            ->has('events', 3)
    );
  }
}
