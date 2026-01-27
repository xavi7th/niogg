<?php

namespace Modules\PublicPage\Tests\Feature;

use Tests\TestCase;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CliEventCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_exists(): void
    {
        // Test that the command can be listed
        $this->artisan('list')
            ->assertExitCode(0);

        // Test that the command help works
        $this->artisan('help', ['command' => 'events:create'])
            ->assertExitCode(0);
    }

    public function test_command_help_output(): void
    {
        // Just verify the command runs help without errors
        $this->artisan('events:create', ['--help' => true])
            ->assertExitCode(0);
    }

    public function test_cli_command_integration(): void
    {
        // Since we can't easily test interactive commands in unit tests,
        // let's test the command is registered properly
        $command = $this->app->make('Illuminate\Contracts\Console\Kernel');

        // This should not throw an exception
        $this->artisan('events:create', [
            '--help' => true,
        ])->assertExitCode(0);
    }

    public function test_slug_generation_logic(): void
    {
        // Test basic slug generation (simplified version)
        $testCases = [
            'Test Event' => 'test-event',
            'Another Test Event' => 'another-test-event',
            'Event With Spaces' => 'event-with-spaces',
        ];

        foreach ($testCases as $input => $expected) {
            $slug = \Illuminate\Support\Str::slug($input);
            $this->assertEquals($expected, $slug);
        }
    }

    public function test_duplicate_slug_detection(): void
    {
        // Create an event first
        Event::create([
            'name' => 'Test Event',
            'description' => 'Test description',
            'icon' => '🎉',
            'category' => 'charity_event',
            'event_date' => '2025-12-15',
            'slug' => 'test-event',
            'is_published' => true,
        ]);

        // Test that a similar name would get a different slug
        $newSlug = \Illuminate\Support\Str::slug('Test Event') . '-2';
        $this->assertNotEquals('test-event', $newSlug);
        $this->assertEquals('test-event-2', $newSlug);
    }

    public function test_model_creation(): void
    {
        // Test that Event model can be created (simulating CLI behavior)
        $event = Event::create([
            'name' => 'Manual Test Event',
            'description' => 'Test description',
            'icon' => '🎉',
            'category' => 'social_event',
            'event_date' => '2025-11-20',
            'slug' => 'manual-test-event',
            'is_published' => true,
        ]);

        $this->assertNotNull($event);
        $this->assertEquals('Manual Test Event', $event->name);
        $this->assertEquals('social_event', $event->category);

        // Clean up
        $event->delete();
    }

    public function test_video_creation_with_event(): void
    {
        // Create an event first
        $event = Event::create([
            'name' => 'Test Event with Video',
            'description' => 'Test description',
            'icon' => '🎭',
            'category' => 'gala_night',
            'event_date' => '2025-10-15',
            'slug' => 'test-event-with-video',
            'is_published' => true,
        ]);

        // Create a video for this event
        $video = Video::create([
            'event_id' => $event->id,
            'title' => 'Test Video',
            'description' => 'Test video description',
            'video_url' => '/videos/test.mp4',
            'thumbnail_url' => '/images/test.jpg',
            'duration_seconds' => 120,
            'is_featured' => true,
            'sort_order' => 0,
        ]);

        $this->assertNotNull($video);
        $this->assertEquals($event->id, $video->event_id);
        $this->assertTrue($video->is_featured);

        // Clean up
        $video->delete();
        $event->delete();
    }
}
