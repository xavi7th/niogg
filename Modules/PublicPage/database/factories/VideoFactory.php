<?php

namespace Modules\PublicPage\Database\Factories;

use Modules\PublicPage\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    protected $model = Video::class;

    public function definition(): array
    {
        return [
            'event_id' => NULL, // Will be set by for() relationship
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'video_url' => fake()->url(),
            'thumbnail_url' => fake()->imageUrl(640, 360),
            'duration_seconds' => fake()->numberBetween(60, 7200),
            'is_featured' => fake()->boolean(20),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => TRUE,
        ]);
    }
}
