<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Modules\PublicPage\Models\Event;
use Illuminate\Support\Facades\Storage;
use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPhotoControllerTest extends TestCase
{
  use RefreshDatabase;

  private User $admin;

  protected function setUp(): void
  {
    parent::setUp();
    Storage::fake('public');
    $this->admin = User::factory()->create(['is_admin' => TRUE]);
  }

  public function test_unauthenticated_user_cannot_upload_photos(): void
  {
    $event = Event::factory()->create();

    $response = $this->postJson(route('admin.events.photos.store', $event), [
      'photos' => [UploadedFile::fake()->image('photo.jpg')],
    ]);

    $response->assertUnauthorized();
  }

  public function test_non_admin_cannot_upload_photos(): void
  {
    $user = User::factory()->create(['is_admin' => FALSE, 'is_super_admin' => FALSE]);
    $event = Event::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('admin.events.photos.store', $event), [
          'photos' => [UploadedFile::fake()->image('photo.jpg')],
        ]);

    $response->assertForbidden();
  }

  public function test_admin_can_upload_single_photo(): void
  {
    $event = Event::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.store', $event), [
          'photos' => [$file],
        ]);

    $response->assertOk()
        ->assertJsonStructure(['message', 'photos']);

    $this->assertDatabaseHas('event_photos', ['event_id' => $event->id]);
    $this->assertCount(1, $event->photos()->get());
  }

  public function test_admin_can_bulk_upload_multiple_photos(): void
  {
    $event = Event::factory()->create();
    $files = [
      UploadedFile::fake()->image('photo1.jpg', 50, 50),
      UploadedFile::fake()->image('photo2.jpg', 50, 50),
      UploadedFile::fake()->image('photo3.jpg', 50, 50),
    ];

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.store', $event), [
          'photos' => $files,
        ]);

    $response->assertOk();
    $this->assertCount(3, $event->photos()->get());
  }

  public function test_sort_order_is_sequential_for_bulk_upload(): void
  {
    $event = Event::factory()->create();
    $files = [
      UploadedFile::fake()->image('photo1.jpg', 50, 50),
      UploadedFile::fake()->image('photo2.jpg', 50, 50),
    ];

    $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.store', $event), ['photos' => $files]);

    $orders = $event->photos()->orderBy('sort_order')->pluck('sort_order')->toArray();
    $this->assertEquals([1, 2], $orders);
  }

  public function test_alt_texts_set_on_upload_when_provided(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.store', $event), [
          'photos' => [UploadedFile::fake()->image('photo.jpg', 50, 50)],
          'alt_texts' => ['A beautiful event photo'],
        ]);

    $response->assertOk();
    $this->assertDatabaseHas('event_photos', [
      'event_id' => $event->id,
      'alt_text' => 'A beautiful event photo',
    ]);
  }

  public function test_rejects_non_image_file(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.store', $event), [
          'photos' => [UploadedFile::fake()->create('document.pdf', 100, 'application/pdf')],
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['photos.0']);
  }

  public function test_rejects_file_over_10mb(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.store', $event), [
          'photos' => [UploadedFile::fake()->create('big.jpg', 11000, 'image/jpeg')],
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['photos.0']);
  }

  public function test_admin_can_update_alt_text(): void
  {
    $photo = EventPhoto::factory()->for(Event::factory())->create(['alt_text' => NULL]);

    $response = $this->actingAs($this->admin)
        ->putJson(route('admin.photos.update', $photo), [
          'alt_text' => 'Updated description',
        ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Photo updated.');

    $this->assertDatabaseHas('event_photos', [
      'id' => $photo->id,
      'alt_text' => 'Updated description',
    ]);
  }

  public function test_admin_can_reorder_photos(): void
  {
    $event = Event::factory()->create();
    $photo1 = EventPhoto::factory()->for($event)->create(['sort_order' => 0]);
    $photo2 = EventPhoto::factory()->for($event)->create(['sort_order' => 1]);
    $photo3 = EventPhoto::factory()->for($event)->create(['sort_order' => 2]);

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.reorder', $event), [
          'photo_ids' => [$photo3->id, $photo1->id, $photo2->id],
        ]);

    $response->assertOk()
        ->assertJsonPath('message', 'Photos reordered.');

    $this->assertDatabaseHas('event_photos', ['id' => $photo3->id, 'sort_order' => 0]);
    $this->assertDatabaseHas('event_photos', ['id' => $photo1->id, 'sort_order' => 1]);
    $this->assertDatabaseHas('event_photos', ['id' => $photo2->id, 'sort_order' => 2]);
  }

  public function test_admin_can_delete_photo(): void
  {
    $photo = EventPhoto::factory()->for(Event::factory())->create();

    $response = $this->actingAs($this->admin)
        ->deleteJson(route('admin.photos.destroy', $photo));

    $response->assertOk()
        ->assertJsonPath('message', 'Photo deleted.');

    $this->assertDatabaseMissing('event_photos', ['id' => $photo->id]);
  }

  public function test_reorder_requires_photo_ids_array(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.reorder', $event), []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['photo_ids']);
  }

  public function test_reorder_rejects_non_existent_photo_id(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.reorder', $event), [
          'photo_ids' => [99999],
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['photo_ids.0']);
  }

  public function test_cannot_upload_zero_photos(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.store', $event), [
          'photos' => [],
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['photos']);
  }

  public function test_cannot_upload_oversized_photo(): void
  {
    $event = Event::factory()->create();

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.events.photos.store', $event), [
          'photos' => [UploadedFile::fake()->image('large.jpg')->size(10241)],
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['photos.0']);
  }
}
