<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Events table - add missing indexes for search performance
        Schema::table('events', function (Blueprint $table): void {
            $table->index('name');
            $table->index('is_published');
            // Composite index for common filter pattern (published events by date)
            $table->index(['is_published', 'event_date']);
        });

        // Videos table - add missing indexes for search performance
        Schema::table('videos', function (Blueprint $table): void {
            $table->index('title');
            // Composite index for featured videos sort order
            $table->index(['event_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropIndex(['name']);
            $table->dropIndex(['is_published']);
            $table->dropIndex(['is_published', 'event_date']);
        });

        Schema::table('videos', function (Blueprint $table): void {
            $table->dropIndex(['title']);
            $table->dropIndex(['event_id', 'sort_order']);
        });
    }
};
