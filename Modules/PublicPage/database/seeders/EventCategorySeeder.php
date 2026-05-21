<?php

namespace Modules\PublicPage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PublicPage\Models\Event;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('event_categories') as $category) {
            if (Event::where('category', $category)->exists()) {
                continue;
            }

            Event::factory()->create([
                'name' => $category . ' Event (Placeholder)',
                'category' => $category,
                'is_published' => FALSE,
            ]);
        }
    }
}
