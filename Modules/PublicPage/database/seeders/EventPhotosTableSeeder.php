<?php

namespace Modules\PublicPage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;

class EventPhotosTableSeeder extends Seeder
{
  public function run(): void
  {
    $events = Event::all();

    foreach ($events as $event) {
      $count = match ($event->category) {
        'Free Medicals' => 10,
        'Awards' => 8,
        'Education' => 6,
        default => 5,
      };

      for ($i = 1; $i <= $count; $i++) {
        EventPhoto::create([
          'event_id' => $event->id,
          'photo_url' => '/storage/event-photos/' . $event->slug . '-' . $i . '.jpg',
          'thumbnail_url' => '/storage/event-photos/thumbnails/' . $event->slug . '-' . $i . '.jpg',
          'alt_text' => $i === 1 ? $this->getFeaturedAlt($event->name) : 'Photo ' . $i . ' from ' . $event->name,
          'sort_order' => $i - 1,
        ]);
      }
    }
  }

  private function getFeaturedAlt(string $eventName): string
  {
    return match (TRUE) {
      str_contains($eventName, 'Medical') => 'Medical team serving the community',
      str_contains($eventName, 'Awards') => 'Award ceremony highlights',
      str_contains($eventName, 'Youth') => 'Youth leaders during the summit',
      default => $eventName . ' - Featured Photo',
    };
  }
}
