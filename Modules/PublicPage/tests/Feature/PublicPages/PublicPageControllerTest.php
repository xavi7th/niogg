<?php

namespace Modules\PublicPage\Tests\Feature\PublicPages;

use Illuminate\Support\Facades\Mail;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Modules\PublicPage\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Emails\NewContactFormMessage;

class PublicPageControllerTest extends TestCase
{
  use RefreshDatabase;

  public function test_home_page_renders_successfully(): void
  {
    $response = $this->get(route('app.index'));

    $response->assertStatus(200);
  }

  public function test_about_page_renders_successfully(): void
  {
    $response = $this->get(route('app.about'));

    $response->assertStatus(200);
  }

  public function test_gallery_page_renders_successfully(): void
  {
    $response = $this->get(route('app.gallery'));

    $response->assertStatus(200);
  }

  public function test_contact_page_renders_successfully(): void
  {
    $response = $this->get(route('app.contact'));

    $response->assertStatus(200);
  }

  public function test_contact_form_submission_sends_email(): void
  {
    Mail::fake();

    $response = $this->post(route('app.contact.store'), [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'subject' => 'Test Subject',
      'message' => 'Test message content',
    ]);

    $response->assertSessionHas('success');
    $response->assertRedirect(route('app.contact'));

    Mail::assertSent(NewContactFormMessage::class, fn ($mail) => $mail->hasTo(config('mail.contact_email'))
        && $mail->subject === 'New Contact Form Message: Test Subject');
  }

  public function test_contact_form_requires_valid_name(): void
  {
    $response = $this->post(route('app.contact.store'), [
      'name' => '',
      'email' => 'john@example.com',
      'subject' => 'Test',
      'message' => 'Test message',
    ]);

    $response->assertSessionHasErrors(['name']);
    Mail::assertNothingSent();
  }

  public function test_contact_form_requires_valid_email(): void
  {
    $response = $this->post(route('app.contact.store'), [
      'name' => 'John Doe',
      'email' => 'not-an-email',
      'subject' => 'Test',
      'message' => 'Test message',
    ]);

    $response->assertSessionHasErrors(['email']);
    Mail::assertNothingSent();
  }

  public function test_contact_form_requires_message(): void
  {
    $response = $this->post(route('app.contact.store'), [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'subject' => 'Test',
      'message' => '',
    ]);

    $response->assertSessionHasErrors(['message']);
    Mail::assertNothingSent();
  }

  public function test_media_showcase_page_renders_with_events(): void
  {
    $event = Event::factory()->published()->create();
    Video::factory()->forEvent($event)->create();

    $response = $this->get(route('events.media-showcase'));

    $response->assertStatus(200);
  }

  public function test_media_showcase_shows_only_published_events(): void
  {
    Event::factory()->published()->create(['name' => 'Published Event']);
    Event::factory()->draft()->create(['name' => 'Draft Event']);

    $response = $this->get(route('events.media-showcase'));

    $response->assertStatus(200);
  }

  public function test_event_detail_page_renders(): void
  {
    $event = Event::factory()->published()->create();

    $response = $this->get(route('events.show', $event));

    $response->assertStatus(200);
  }

  public function test_event_detail_returns_404_for_non_existent_slug(): void
  {
    $response = $this->get(route('events.show', ['event' => 'non-existent']));

    $response->assertStatus(404);
  }
}
