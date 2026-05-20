<?php

namespace Modules\PublicPage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PublicPage\Models\Event;

class EventsTableSeeder extends Seeder
{
  public function run(): void
  {
    $events = [
      [
        'name' => 'Free Medical Outreach 2025',
        'description' => 'Our annual medical outreach program bringing free healthcare services to underserved communities.',
        'icon' => '🏥',
        'category' => 'Free Medicals',
        'event_date' => '2025-11-15',
        'is_published' => TRUE,
      ],
      [
        'name' => 'Excellence Awards & Fundraising Gala',
        'description' => 'An evening of celebration recognizing outstanding contributions to governance and development in Nigeria.',
        'icon' => '🏆',
        'category' => 'Awards',
        'event_date' => '2025-10-20',
        'is_published' => TRUE,
      ],
      [
        'name' => 'Youth Leadership Summit 2025',
        'description' => 'Empowering the next generation of leaders through workshops, mentorship, and networking.',
        'icon' => '🎓',
        'category' => 'Education',
        'event_date' => '2025-09-10',
        'is_published' => TRUE,
      ],
    ];

    foreach ($events as $event) {
      Event::create($event);
    }
  }
}
