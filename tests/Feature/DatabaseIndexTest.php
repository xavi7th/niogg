<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_events_table_has_name_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('events', 'events_name_index'),
            'Events table should have an index on the name column'
        );
    }

    public function test_events_table_has_is_published_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('events', 'events_is_published_index'),
            'Events table should have an index on the is_published column'
        );
    }

    public function test_events_table_has_composite_is_published_event_date_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('events', 'events_is_published_event_date_index'),
            'Events table should have a composite index on is_published and event_date columns'
        );
    }

    public function test_videos_table_has_title_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('videos', 'videos_title_index'),
            'Videos table should have an index on the title column'
        );
    }

    public function test_videos_table_has_composite_event_id_sort_order_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('videos', 'videos_event_id_sort_order_index'),
            'Videos table should have a composite index on event_id and sort_order columns'
        );
    }

    public function test_events_table_has_slug_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('events', 'events_slug_index'),
            'Events table should have an index on the slug column'
        );
    }

    public function test_events_table_has_category_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('events', 'events_category_index'),
            'Events table should have an index on the category column'
        );
    }

    public function test_events_table_has_event_date_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('events', 'events_event_date_index'),
            'Events table should have an index on the event_date column'
        );
    }

    public function test_videos_table_has_event_id_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('videos', 'videos_event_id_index'),
            'Videos table should have an index on the event_id column'
        );
    }

    public function test_videos_table_has_is_featured_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('videos', 'videos_is_featured_index'),
            'Videos table should have an index on the is_featured column'
        );
    }

    public function test_videos_table_has_sort_order_index(): void
    {
        $this->assertTrue(
            $this->hasIndex('videos', 'videos_sort_order_index'),
            'Videos table should have an index on the sort_order column'
        );
    }

    /**
     * Check if a table has a specific index.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = collect(DB::select("SHOW INDEX FROM {$table}"))->pluck('Key_name')->unique()->toArray();

        return in_array($indexName, $indexes, TRUE);
    }
}
