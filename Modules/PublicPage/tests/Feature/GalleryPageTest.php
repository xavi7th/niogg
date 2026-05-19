<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GalleryPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['inertia.testing.ensure_pages_exist' => FALSE]);
    }

    public function test_gallery_page_returns_200(): void
    {
        $response = $this->get(route('app.gallery'));

        $response->assertOk();
    }

    public function test_gallery_response_includes_paginated_photos(): void
    {
        $event = Event::factory()->published()->create();
        EventPhoto::factory()->for($event)->count(3)->create();

        $response = $this->get(route('app.gallery'));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->component('PublicPage::Gallery')
                    ->has('photos')
                    ->has('photos.data', 3)
            );
    }

    public function test_gallery_response_includes_categories_from_published_events_with_photos(): void
    {
        $event = Event::factory()->published()->create(['category' => 'Conference']);
        EventPhoto::factory()->for($event)->create();

        $response = $this->get(route('app.gallery'));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->has('categories')
                    ->where('categories', fn ($categories) => in_array('Conference', $categories->toArray()))
            );
    }

    public function test_filter_by_category_returns_only_matching_photos(): void
    {
        $conference = Event::factory()->published()->create(['category' => 'Conference']);
        $awards = Event::factory()->published()->create(['category' => 'Awards']);
        EventPhoto::factory()->for($conference)->count(2)->create();
        EventPhoto::factory()->for($awards)->count(3)->create();

        $response = $this->get(route('app.gallery', ['category' => 'Conference']));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->has('photos.data', 2)
                    ->where('activeCategory', 'Conference')
            );
    }

    public function test_empty_state_when_no_photos_exist(): void
    {
        $response = $this->get(route('app.gallery'));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->has('photos.data', 0)
            );
    }

    public function test_photos_from_unpublished_events_are_excluded(): void
    {
        $published = Event::factory()->published()->create();
        $unpublished = Event::factory()->draft()->create();
        EventPhoto::factory()->for($published)->count(2)->create();
        EventPhoto::factory()->for($unpublished)->count(5)->create();

        $response = $this->get(route('app.gallery'));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->has('photos.data', 2)
            );
    }

    public function test_categories_from_unpublished_events_are_excluded(): void
    {
        $unpublished = Event::factory()->draft()->create(['category' => 'Sports']);
        EventPhoto::factory()->for($unpublished)->create();

        $response = $this->get(route('app.gallery'));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->where('categories', fn ($categories) => ! in_array('Sports', $categories->toArray()))
            );
    }

    public function test_gallery_paginates_at_24_per_page(): void
    {
        $event = Event::factory()->published()->create();
        EventPhoto::factory()->for($event)->count(30)->create();

        $response = $this->get(route('app.gallery'));

        $response->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->has('photos.data', 24)
                    ->has('photos.next_page_url')
            );
    }
}
