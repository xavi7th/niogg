<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use App\Models\User;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVideoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_video_routes(): void
    {
        $event = Event::factory()->create();

        $response = $this->post(route('admin.videos.store', $event), [
            'title' => 'Test Video',
            'video_url' => 'https://example.com/video.mp4',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_access_video_routes(): void
    {
        $user = User::factory()->create([
            'is_admin' => FALSE,
            'is_super_admin' => FALSE,
        ]);
        $event = Event::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.videos.store', $event), [
                'title' => 'Test Video',
                'video_url' => 'https://example.com/video.mp4',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_video_for_event(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);
        $event = Event::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.videos.store', $event), [
                'title' => 'Test Video',
                'description' => 'Test description',
                'video_url' => 'https://example.com/video.mp4',
                'thumbnail_url' => 'https://example.com/thumb.jpg',
                'duration_seconds' => 120,
                'is_featured' => TRUE,
                'sort_order' => 1,
            ]);

        $response->assertRedirect(route('admin.events.show', $event));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('videos', [
            'event_id' => $event->id,
            'title' => 'Test Video',
            'video_url' => 'https://example.com/video.mp4',
            'is_featured' => 1,
            'sort_order' => 1,
        ]);
    }

    public function test_super_admin_can_create_video(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => TRUE,
        ]);
        $event = Event::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.videos.store', $event), [
                'title' => 'Test Video',
                'video_url' => 'https://example.com/video.mp4',
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('videos', [
            'event_id' => $event->id,
            'title' => 'Test Video',
        ]);
    }

    public function test_admin_can_update_video(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);
        $video = Video::factory()->for(Event::factory())->create();

        $response = $this->actingAs($user)
            ->put(route('admin.videos.update', $video), [
                'title' => 'Updated Title',
                'description' => 'Updated description',
                'video_url' => 'https://example.com/updated.mp4',
                'is_featured' => TRUE,
                'sort_order' => 5,
            ]);

        $response->assertRedirect(route('admin.events.show', $video->event));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'title' => 'Updated Title',
            'video_url' => 'https://example.com/updated.mp4',
            'sort_order' => 5,
        ]);
    }

    public function test_admin_can_delete_video(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);
        $video = Video::factory()->for(Event::factory())->create();

        $response = $this->actingAs($user)
            ->delete(route('admin.videos.destroy', $video));

        $response->assertRedirect(route('admin.events.show', $video->event));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('videos', [
            'id' => $video->id,
        ]);
    }

    public function test_video_creation_requires_title(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);
        $event = Event::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.videos.store', $event), [
                'video_url' => 'https://example.com/video.mp4',
            ]);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_video_creation_requires_valid_video_url(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);
        $event = Event::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.videos.store', $event), [
                'title' => 'Test Video',
                'video_url' => 'not-a-valid-url',
            ]);

        $response->assertSessionHasErrors(['video_url']);
    }

    public function test_thumbnail_url_must_be_valid_when_provided(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);
        $event = Event::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('admin.videos.store', $event), [
                'title' => 'Test Video',
                'video_url' => 'https://example.com/video.mp4',
                'thumbnail_url' => 'not-a-url',
            ]);

        $response->assertSessionHasErrors(['thumbnail_url']);
    }
}
