<?php

namespace Modules\PublicPage\Tests\Feature\FormRequests;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoUploadRequestTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->admin = User::factory()->admin()->create();
  }

  public function test_initialize_requires_filename(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.videos.upload', $event), [
          'action' => 'initialize',
          'filename' => '',
          'file_size' => 1000,
          'mime_type' => 'video/mp4',
        ]);

    $response->assertStatus(422);
  }

  public function test_initialize_requires_valid_file_size(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.videos.upload', $event), [
          'action' => 'initialize',
          'filename' => 'test.mp4',
          'file_size' => 0,
          'mime_type' => 'video/mp4',
        ]);

    $response->assertStatus(422);
  }

  public function test_initialize_requires_valid_mime_type(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.videos.upload', $event), [
          'action' => 'initialize',
          'filename' => 'test.mp4',
          'file_size' => 1000,
          'mime_type' => 'video/avi',
        ]);

    $response->assertStatus(422);
  }

  public function test_chunk_requires_upload_id(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.videos.upload', $event), [
          'action' => 'chunk',
          'upload_id' => '',
          'chunk' => UploadedFile::fake()->create('chunk.mp4', 1024),
          'chunk_index' => 0,
          'total_chunks' => 1,
        ]);

    $response->assertStatus(422);
  }

  public function test_chunk_requires_chunk_index(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.videos.upload', $event), [
          'action' => 'chunk',
          'upload_id' => (string) \Illuminate\Support\Str::uuid(),
          'chunk' => UploadedFile::fake()->create('chunk.mp4', 1024),
          'chunk_index' => '',
          'total_chunks' => 1,
        ]);

    $response->assertStatus(422);
  }

  public function test_finalize_requires_title(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.videos.upload', $event), [
          'action' => 'finalize',
          'upload_id' => (string) \Illuminate\Support\Str::uuid(),
          'title' => '',
        ]);

    $response->assertStatus(422);
  }

  public function test_requires_valid_action(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.videos.upload', $event), [
          'action' => 'invalid',
        ]);

    $response->assertStatus(422);
  }
}
