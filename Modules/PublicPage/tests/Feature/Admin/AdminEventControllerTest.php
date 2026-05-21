<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Support\Facades\Cache;
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

    public function test_index_orders_events_by_created_at_descending(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event1 = Event::factory()->create(['created_at' => now()->subDays(5)]);
        $event2 = Event::factory()->create(['created_at' => now()->subDays(1)]);
        $event3 = Event::factory()->create(['created_at' => now()->subDays(10)]);

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

    public function test_destroy_requires_authentication(): void
    {
        $event = Event::factory()->create();

        $response = $this->delete("/admin/events/{$event->id}");

        $response->assertRedirect('/login');
    }

    public function test_destroy_requires_admin_role(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
            'is_super_admin' => FALSE,
        ]);

        $event = Event::factory()->create();

        $response = $this->actingAs($user)->delete("/admin/events/{$event->id}");

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_event(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event = Event::factory()->create();

        $response = $this->actingAs($user)->delete("/admin/events/{$event->id}");

        $response->assertRedirect('/admin/events');
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_destroy_cascades_to_videos(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event = Event::factory()->create();
        $video1 = Video::factory()->for($event)->create();
        $video2 = Video::factory()->for($event)->create();

        $this->assertDatabaseHas('videos', ['id' => $video1->id]);
        $this->assertDatabaseHas('videos', ['id' => $video2->id]);

        $response = $this->actingAs($user)->delete("/admin/events/{$event->id}");

        $response->assertRedirect('/admin/events');
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        $this->assertDatabaseMissing('videos', ['id' => $video1->id]);
        $this->assertDatabaseMissing('videos', ['id' => $video2->id]);
    }

    public function test_destroy_returns_success_flash_message(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event = Event::factory()->create();

        $response = $this->actingAs($user)->delete("/admin/events/{$event->id}");

        $response->assertRedirect('/admin/events')
            ->assertSessionHas('success', 'Event deleted successfully.');
    }

    public function test_index_caches_paginated_results(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        Event::factory()->count(20)->create();

        Cache::flush();

        $this->actingAs($user)->get('/admin/events?page=1');
        $cacheKey = 'admin.events.list:page:1:per_page:15';

        $this->assertNotNull(Cache::get($cacheKey));
    }

    public function test_store_invalidates_cache(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        Event::factory()->count(5)->create();

        Cache::flush();

        $this->actingAs($user)->get('/admin/events?page=1');
        $cacheKey = 'admin.events.list:page:1:per_page:15';
        $this->assertNotNull(Cache::get($cacheKey));

        $this->actingAs($user)->post('/admin/events', [
            'name' => 'New Event',
            'category' => 'Conference',
            'event_date' => '2025-01-01',
            'is_published' => FALSE,
        ]);

        $this->assertNull(Cache::get($cacheKey));
    }

    public function test_update_invalidates_cache(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event = Event::factory()->create();

        Cache::flush();

        $this->actingAs($user)->get('/admin/events?page=1');
        $cacheKey = 'admin.events.list:page:1:per_page:15';
        $this->assertNotNull(Cache::get($cacheKey));

        $this->actingAs($user)->put("/admin/events/{$event->id}", [
            'name' => 'Updated Event',
            'category' => 'Conference',
            'event_date' => '2025-01-01',
            'is_published' => FALSE,
        ]);

        $this->assertNull(Cache::get($cacheKey));
    }

    public function test_destroy_invalidates_cache(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event = Event::factory()->create();

        Cache::flush();

        $this->actingAs($user)->get('/admin/events?page=1');
        $cacheKey = 'admin.events.list:page:1:per_page:15';
        $this->assertNotNull(Cache::get($cacheKey));

        $this->actingAs($user)->delete("/admin/events/{$event->id}");

        $this->assertNull(Cache::get($cacheKey));
    }

    public function test_cache_has_ttl_of_one_hour(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        Event::factory()->count(5)->create();

        Cache::flush();

        $this->actingAs($user)->get('/admin/events?page=1');
        $cacheKey = 'admin.events.list:page:1:per_page:15';

        $cached = Cache::get($cacheKey);
        $this->assertNotNull($cached);
    }

    public function test_bulk_publish_requires_authentication(): void
    {
        $response = $this->post('/admin/events/bulk/publish', [
            'event_ids' => [1, 2, 3],
        ]);

        $response->assertRedirect('/login');
    }

    public function test_bulk_publish_requires_admin_role(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
            'is_super_admin' => FALSE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events/bulk/publish', [
            'event_ids' => [1, 2, 3],
        ]);

        $response->assertStatus(403);
    }

    public function test_bulk_publish_requires_event_ids_array(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->postJson('/admin/events/bulk/publish', [
            'event_ids' => 'not-an-array',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['event_ids']);
    }

    public function test_bulk_publish_publishes_events(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event1 = Event::factory()->create(['is_published' => FALSE]);
        $event2 = Event::factory()->create(['is_published' => FALSE]);
        $event3 = Event::factory()->create(['is_published' => FALSE]);

        $response = $this->actingAs($user)->postJson('/admin/events/bulk/publish', [
            'event_ids' => [$event1->id, $event2->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => '2 event(s) published successfully.',
                'count' => 2,
            ]);

        $this->assertDatabaseHas('events', ['id' => $event1->id, 'is_published' => TRUE]);
        $this->assertDatabaseHas('events', ['id' => $event2->id, 'is_published' => TRUE]);
        $this->assertDatabaseHas('events', ['id' => $event3->id, 'is_published' => FALSE]);
    }

    public function test_bulk_publish_invalidates_cache(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event1 = Event::factory()->create(['is_published' => FALSE]);

        Cache::flush();

        $this->actingAs($user)->get('/admin/events?page=1');
        $cacheKey = 'admin.events.list:page:1:per_page:15';
        $this->assertNotNull(Cache::get($cacheKey));

        $this->actingAs($user)->post('/admin/events/bulk/publish', [
            'event_ids' => [$event1->id],
        ]);

        $this->assertNull(Cache::get($cacheKey));
    }

    public function test_bulk_unpublish_requires_authentication(): void
    {
        $response = $this->post('/admin/events/bulk/unpublish', [
            'event_ids' => [1, 2, 3],
        ]);

        $response->assertRedirect('/login');
    }

    public function test_bulk_unpublish_requires_admin_role(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
            'is_super_admin' => FALSE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events/bulk/unpublish', [
            'event_ids' => [1, 2, 3],
        ]);

        $response->assertStatus(403);
    }

    public function test_bulk_unpublish_unpublishes_events(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event1 = Event::factory()->create(['is_published' => TRUE]);
        $event2 = Event::factory()->create(['is_published' => TRUE]);
        $event3 = Event::factory()->create(['is_published' => TRUE]);

        $response = $this->actingAs($user)->postJson('/admin/events/bulk/unpublish', [
            'event_ids' => [$event1->id, $event2->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => '2 event(s) unpublished successfully.',
                'count' => 2,
            ]);

        $this->assertDatabaseHas('events', ['id' => $event1->id, 'is_published' => FALSE]);
        $this->assertDatabaseHas('events', ['id' => $event2->id, 'is_published' => FALSE]);
        $this->assertDatabaseHas('events', ['id' => $event3->id, 'is_published' => TRUE]);
    }

    public function test_bulk_unpublish_invalidates_cache(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event1 = Event::factory()->create(['is_published' => TRUE]);

        Cache::flush();

        $this->actingAs($user)->get('/admin/events?page=1');
        $cacheKey = 'admin.events.list:page:1:per_page:15';
        $this->assertNotNull(Cache::get($cacheKey));

        $this->actingAs($user)->post('/admin/events/bulk/unpublish', [
            'event_ids' => [$event1->id],
        ]);

        $this->assertNull(Cache::get($cacheKey));
    }

    public function test_bulk_delete_requires_authentication(): void
    {
        $response = $this->delete('/admin/events/bulk', [
            'event_ids' => [1, 2, 3],
        ]);

        $response->assertRedirect('/login');
    }

    public function test_bulk_delete_requires_admin_role(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
            'is_super_admin' => FALSE,
        ]);

        $response = $this->actingAs($user)->delete('/admin/events/bulk', [
            'event_ids' => [1, 2, 3],
        ]);

        $response->assertStatus(403);
    }

    public function test_bulk_delete_deletes_events(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event1 = Event::factory()->create();
        $event2 = Event::factory()->create();
        $event3 = Event::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/admin/events/bulk', [
            'event_ids' => [$event1->id, $event2->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => '2 event(s) deleted successfully.',
                'count' => 2,
            ]);

        $this->assertDatabaseMissing('events', ['id' => $event1->id]);
        $this->assertDatabaseMissing('events', ['id' => $event2->id]);
        $this->assertDatabaseHas('events', ['id' => $event3->id]);
    }

    public function test_bulk_delete_cascades_to_videos(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event1 = Event::factory()->create();
        $video1 = Video::factory()->for($event1)->create();
        $video2 = Video::factory()->for($event1)->create();

        $response = $this->actingAs($user)->deleteJson('/admin/events/bulk', [
            'event_ids' => [$event1->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('events', ['id' => $event1->id]);
        $this->assertDatabaseMissing('videos', ['id' => $video1->id]);
        $this->assertDatabaseMissing('videos', ['id' => $video2->id]);
    }

    public function test_bulk_delete_invalidates_cache(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $event1 = Event::factory()->create();

        Cache::flush();

        $this->actingAs($user)->get('/admin/events?page=1');
        $cacheKey = 'admin.events.list:page:1:per_page:15';
        $this->assertNotNull(Cache::get($cacheKey));

        $this->actingAs($user)->delete('/admin/events/bulk', [
            'event_ids' => [$event1->id],
        ]);

        $this->assertNull(Cache::get($cacheKey));
    }
}
