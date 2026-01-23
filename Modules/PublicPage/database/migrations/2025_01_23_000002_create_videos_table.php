<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('videos', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
      $table->string('title');
      $table->text('description')->nullable();
      $table->string('video_url');
      $table->string('thumbnail_url')->nullable();
      $table->integer('duration_seconds')->default(0);
      $table->boolean('is_featured')->default(FALSE);
      $table->integer('sort_order')->default(0);
      $table->timestamps();

      $table->index('event_id');
      $table->index('is_featured');
      $table->index('sort_order');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('videos');
  }
};
