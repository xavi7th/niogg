<?php

namespace Modules\PublicPage\Database\Factories;

use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\EventPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventPhotoFactory extends Factory
{
    protected $model = EventPhoto::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'photo_url' => '/storage/event-photos/' . $this->faker->uuid() . '.jpg',
            'thumbnail_url' => '/storage/event-photos/thumbnails/' . $this->faker->uuid() . '.jpg',
            'alt_text' => $this->faker->optional()->sentence(4),
            'sort_order' => 0,
        ];
    }

    public function sortOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $order,
        ]);
    }
}
