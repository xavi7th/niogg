<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventDetailPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['inertia.testing.ensure_pages_exist' => FALSE]);
    }

    public function test_published_event_returns_200(): void
    {
        $event = Event::factory()->published()->create();

        $response = $this->get(route('events.show', $event));

        $response->assertOk();
    }

    public function test_unpublished_event_returns_404(): void
    {
        $event = Event::factory()->draft()->create();

        $response = $this->get(route('events.show', $event));

        $response->assertNotFound();
    }

    public function test_response_includes_photos_ordered_by_sort_order(): void
    {
        $event = Event::factory()->published()->create();
        $third = EventPhoto::factory()->for($event)->create(['sort_order' => 3]);
        $first = EventPhoto::factory()->for($event)->create(['sort_order' => 1]);
        $second = EventPhoto::factory()->for($event)->create(['sort_order' => 2]);

        $response = $this->get(route('events.show', $event));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->component('PublicPage::EventDetail')
                    ->has('event.photos', 3)
                    ->where('event.photos.0.id', $first->id)
                    ->where('event.photos.1.id', $second->id)
                    ->where('event.photos.2.id', $third->id)
            );
    }

    public function test_response_includes_videos_ordered_by_sort_order(): void
    {
        $event = Event::factory()->published()->create();
        $second = Video::factory()->for($event)->create(['sort_order' => 2]);
        $first = Video::factory()->for($event)->create(['sort_order' => 1]);

        $response = $this->get(route('events.show', $event));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->has('event.videos', 2)
                    ->where('event.videos.0.id', $first->id)
                    ->where('event.videos.1.id', $second->id)
            );
    }

    public function test_event_with_no_photos_returns_empty_photos_array(): void
    {
        $event = Event::factory()->published()->create();

        $response = $this->get(route('events.show', $event));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->has('event.photos', 0)
            );
    }

    public function test_event_with_no_videos_returns_empty_videos_array(): void
    {
        $event = Event::factory()->published()->create();

        $response = $this->get(route('events.show', $event));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->has('event.videos', 0)
            );
    }

    public function test_non_existent_event_slug_returns_404(): void
    {
        $response = $this->get('/events/does-not-exist');

        $response->assertNotFound();
    }

    public function test_event_response_includes_page_title(): void
    {
        $event = Event::factory()->published()->create(['name' => 'Annual Conference 2025']);

        $response = $this->get(route('events.show', $event));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->where('pageTitle', 'Annual Conference 2025')
            );
    }
}
