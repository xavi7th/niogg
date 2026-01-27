<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminEventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get('/admin/events');

        $response->assertRedirect('/login');
    }

    public function test_index_requires_admin_role(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
            'is_super_admin' => FALSE,
        ]);

        $response = $this->actingAs($user)->get('/admin/events');

        $response->assertStatus(403);
    }

    public function test_index_paginates_events_by_15(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        Event::factory()->count(20)->create();

        $response = $this->actingAs($user)->get('/admin/events');

        $response->assertStatus(200);
        $response->assertInertia(function ($page): void {
            $page->has('events')
                ->has('events.data', 15)
                ->has('events.links');
        });
    }

    public function test_index_includes_videos_relationship(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event = Event::factory()->create();
        Video::factory()->count(3)->for($event)->create();

        $response = $this->actingAs($user)->get('/admin/events');

        $response->assertStatus(200);
        $response->assertInertia(function ($page) use ($event): void {
            $page->has('events')
                ->where('events.data.0.id', $event->id)
                ->has('events.data.0.videos', 3);
        });
    }

    public function test_index_orders_events_by_date_descending(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event1 = Event::factory()->create(['event_date' => '2025-01-15']);
        $event2 = Event::factory()->create(['event_date' => '2025-02-01']);
        $event3 = Event::factory()->create(['event_date' => '2025-01-01']);

        $response = $this->actingAs($user)->get('/admin/events');

        $response->assertInertia(function ($page) use ($event2): void {
            $page->where('events.data.0.id', $event2->id);
        });
    }

    public function test_show_requires_authentication(): void
    {
        $event = Event::factory()->create();

        $response = $this->get("/admin/events/{$event->id}");

        $response->assertRedirect('/login');
    }

    public function test_show_requires_admin_role(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
        ]);

        $event = Event::factory()->create();

        $response = $this->actingAs($user)->get("/admin/events/{$event->id}");

        $response->assertStatus(403);
    }

    public function test_show_displays_event_with_videos(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event = Event::factory()->create();
        Video::factory()->count(3)->for($event)->create([
            'sort_order' => 2,
        ]);
        Video::factory()->for($event)->create([
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->get("/admin/events/{$event->id}");

        $response->assertStatus(200);
        $response->assertInertia(function ($page) use ($event): void {
            $page->where('event.id', $event->id)
                ->has('event.videos', 4);
        });
    }

    public function test_show_orders_videos_by_sort_order(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event = Event::factory()->create();
        $video1 = Video::factory()->for($event)->create(['sort_order' => 3]);
        $video2 = Video::factory()->for($event)->create(['sort_order' => 1]);
        $video3 = Video::factory()->for($event)->create(['sort_order' => 2]);

        $response = $this->actingAs($user)->get("/admin/events/{$event->id}");

        $response->assertInertia(function ($page) use ($video2): void {
            $page->where('event.videos.0.id', $video2->id);
        });
    }
}
