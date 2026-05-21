<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeededDataValidationTest extends TestCase
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

  public function test_three_events_seeded_correctly(): void
  {
    $this->assertEquals(3, Event::count());

    $charity = Event::where('category', 'Free Medicals')->first();
    $this->assertNotNull($charity);
    $this->assertEquals('Free Medical Outreach 2025', $charity->name);
    $this->assertEquals('🏥', $charity->icon);

    $gala = Event::where('category', 'Awards')->first();
    $this->assertNotNull($gala);
    $this->assertEquals('Excellence Awards & Fundraising Gala', $gala->name);
    $this->assertEquals('🏆', $gala->icon);

    $social = Event::where('category', 'Education')->first();
    $this->assertNotNull($social);
    $this->assertEquals('Youth Leadership Summit 2025', $social->name);
    $this->assertEquals('🎓', $social->icon);
  }

  public function test_event_dates_correct(): void
  {
    $charity = Event::where('category', 'Free Medicals')->first();
    $this->assertEquals('2025-11-15', $charity->event_date->format('Y-m-d'));

    $gala = Event::where('category', 'Awards')->first();
    $this->assertEquals('2025-10-20', $gala->event_date->format('Y-m-d'));

    $social = Event::where('category', 'Education')->first();
    $this->assertEquals('2025-09-10', $social->event_date->format('Y-m-d'));
  }

  public function test_all_events_published(): void
  {
    Event::all()->each(fn ($event) => $this->assertTrue($event->is_published));
  }

  public function test_total_videos_count_correct(): void
  {
    $this->assertEquals(21, Video::count());
  }

  public function test_videos_per_event_correct(): void
  {
    $charity = Event::where('category', 'Free Medicals')->first();
    $this->assertEquals(8, $charity->videos()->count());

    $gala = Event::where('category', 'Awards')->first();
    $this->assertEquals(7, $gala->videos()->count());

    $social = Event::where('category', 'Education')->first();
    $this->assertEquals(6, $social->videos()->count());
  }

  public function test_each_event_has_one_featured_video(): void
  {
    Event::all()->each(function ($event): void {
      $featured = $event->videos()->where('is_featured', TRUE)->count();
      $this->assertEquals(1, $featured, 'Event ' . $event->name . ' should have exactly 1 featured video');
    });
  }

  public function test_featured_video_titles_correct(): void
  {
    $charity = Event::where('category', 'Free Medicals')->first();
    $charityFeatured = $charity->videos()->where('is_featured', TRUE)->first();
    $this->assertEquals('Medical Outreach Highlights', $charityFeatured->title);

    $gala = Event::where('category', 'Awards')->first();
    $galaFeatured = $gala->videos()->where('is_featured', TRUE)->first();
    $this->assertEquals('Awards Ceremony Highlights', $galaFeatured->title);
  }

  public function test_featured_video_durations_correct(): void
  {
    $charity = Event::where('category', 'Free Medicals')->first();
    $charityFeatured = $charity->videos()->where('is_featured', TRUE)->first();
    $this->assertEquals(765, $charityFeatured->duration_seconds); // 12:45

    $gala = Event::where('category', 'Awards')->first();
    $galaFeatured = $gala->videos()->where('is_featured', TRUE)->first();
    $this->assertEquals(930, $galaFeatured->duration_seconds); // 15:30

    $social = Event::where('category', 'Education')->first();
    $socialFeatured = $social->videos()->where('is_featured', TRUE)->first();
    $this->assertEquals(600, $socialFeatured->duration_seconds); // 10:00
  }

  public function test_video_urls_use_placeholder_format(): void
  {
    Video::all()->each(function ($video): void {
      $this->assertStringStartsWith('/videos/', $video->video_url, 'Video URL should start with /videos/');
      $this->assertStringContainsString('.mp4', $video->video_url);
    });
  }

  public function test_thumbnail_urls_use_placeholder_format(): void
  {
    Video::all()->each(function ($video): void {
      $this->assertStringStartsWith('/images/video-placeholder-', $video->thumbnail_url);
      $this->assertStringContainsString('.jpg', $video->thumbnail_url);
    });
  }

  public function test_non_featured_videos_have_120_second_duration(): void
  {
    $nonFeatured = Video::where('is_featured', FALSE)->get();
    $nonFeatured->each(function ($video): void {
      $this->assertEquals(120, $video->duration_seconds);
    });
  }

  public function test_videos_have_correct_sort_order(): void
  {
    Event::all()->each(function ($event): void {
      $videos = $event->videos()->ordered()->get();
      foreach ($videos as $index => $video) {
        $this->assertEquals($index, $video->sort_order);
      }
    });
  }

  public function test_media_showcase_page_loads_with_seeded_data(): void
  {
    $response = $this->get('/events/media-showcase');
    $response->assertStatus(200);
  }

  public function test_event_specific_grid_page_loads_with_correct_videos(): void
  {
    $event = Event::first();
    $response = $this->get('/events/' . $event->slug . '/videos');
    $response->assertStatus(200);
  }

  public function test_event_slug_generated_from_name(): void
  {
    $charity = Event::where('category', 'Free Medicals')->first();
    $this->assertEquals('free-medical-outreach-2025', $charity->slug);
  }

  public function test_events_ordered_by_date_descending(): void
  {
    $events = Event::ordered()->get();
    $this->assertEquals('2025-11-15', $events[0]->event_date->format('Y-m-d')); // Charity (newest)
    $this->assertEquals('2025-10-20', $events[1]->event_date->format('Y-m-d')); // Gala
    $this->assertEquals('2025-09-10', $events[2]->event_date->format('Y-m-d')); // Social (oldest)
  }

  public function test_seeded_data_persists_across_requests(): void
  {
    // First request
    $response1 = $this->get('/events/media-showcase');
    $response1->assertStatus(200);

    // Second request (verify persistence)
    $response2 = $this->get('/events/media-showcase');
    $response2->assertStatus(200);

    // Verify events still exist in database
    $this->assertEquals(3, Event::count());
    $this->assertEquals(21, Video::count());
  }
}
