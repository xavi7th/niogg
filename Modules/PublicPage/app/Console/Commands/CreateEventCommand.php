<?php

namespace Modules\PublicPage\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;

class CreateEventCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'events:create';

    /**
     * The console command description.
     */
    protected $description = 'Create a new event with interactive prompts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Create New Event ===' . "\n");

        // Get event name
        $name = $this->ask('Event name?');
        while (empty($name)) {
            $this->error('Event name is required.');
            $name = $this->ask('Event name?');
        }

        // Get category
        $categories = [
          'charity_event' => 'Charity Event',
          'gala_night' => 'Gala Night',
          'social_event' => 'Social Event',
          'other' => 'Other',
        ];

        $category = $this->choice(
            'Category?',
            array_values($categories),
            array_key_first($categories)
        );

        // Map display name to internal value
        $internalCategory = array_search($category, $categories);

        // Get description
        $description = $this->ask('Description?') ?? '';

        // Get event date
        $date = $this->ask('Event date (YYYY-MM-DD)?');
        while (empty($date) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error('Please enter a valid date in YYYY-MM-DD format.');
            $date = $this->ask('Event date (YYYY-MM-DD)?');
        }

        // Get icon/emoji
        $icon = $this->ask('Icon/emoji? (optional)', '🎉');

        // Auto-generate slug
        $slug = $this->generateUniqueSlug($name);

        // Create event
        $event = Event::create([
          'name' => $name,
          'description' => $description,
          'icon' => $icon,
          'category' => $internalCategory,
          'event_date' => $date,
          'slug' => $slug,
          'is_published' => TRUE,
        ]);

        $this->info('Event created successfully!');
        $this->line('ID: ' . $event->id);
        $this->line('Name: ' . $event->name);
        $this->line('Slug: ' . $event->slug);

        // Ask about featured video
        if ($this->confirm('Add featured video URL?')) {
            $videoUrl = $this->ask('Video URL?');
            $videoTitle = $this->ask('Video title?') ?? 'Featured Video for ' . $name;
            $duration = $this->ask('Duration in seconds? (optional)', '120');

            Video::create([
              'event_id' => $event->id,
              'title' => $videoTitle,
              'description' => 'Featured video for ' . $name,
              'video_url' => $videoUrl,
              'thumbnail_url' => '/images/video-placeholder-' . rand(1, 10) . '.jpg',
              'duration_seconds' => (int) $duration,
              'is_featured' => TRUE,
              'sort_order' => 0,
            ]);

            $this->info('Featured video added successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Generate a unique slug from the event name.
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Event::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
