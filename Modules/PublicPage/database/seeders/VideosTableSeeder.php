<?php

namespace Modules\PublicPage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;

class VideosTableSeeder extends Seeder
{
  public function run(): void
  {
    $events = Event::all();

    foreach ($events as $event) {
      $count = match ($event->category) {
        'charity_event' => 8,
        'gala_night' => 7,
        'social_event' => 6,
        default => 5,
      };

      for ($i = 1; $i <= $count; $i++) {
        Video::create([
          'event_id' => $event->id,
          'title' => $i === 1 ? $this->getFeaturedTitle($event->name) : "Video {$i} - {$event->name}",
          'description' => "Video {$i} from {$event->name}",
          'video_url' => "/videos/{$event->slug}-{$i}.mp4",
          'thumbnail_url' => "/images/video-placeholder-{$i}.jpg",
          'duration_seconds' => $i === 1 ? $this->getFeaturedDuration($event->category) : 120,
          'is_featured' => $i === 1,
          'sort_order' => $i - 1,
        ]);
      }
    }
  }

  private function getFeaturedTitle(string $eventName): string
  {
    return match (TRUE) {
      str_contains($eventName, 'Medical') => 'Medical Outreach Highlights',
      str_contains($eventName, 'Awards') => 'Awards Ceremony Highlights',
      default => "{$eventName} - Featured",
    };
  }

  private function getFeaturedDuration(string $category): int
  {
    return match ($category) {
      'charity_event' => 765,  // 12:45
      'gala_night' => 930,     // 15:30
      default => 600,          // 10:00
    };
  }
}
