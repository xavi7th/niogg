<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('events', function (Blueprint $table): void {
      $table->id();
      $table->string('name');
      $table->text('description')->nullable();
      $table->string('icon')->nullable();
      $table->string('category')->nullable();
      $table->date('event_date');
      $table->string('slug')->unique();
      $table->boolean('is_published')->default(TRUE);
      $table->timestamps();

      $table->index('slug');
      $table->index('category');
      $table->index('event_date');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('events');
  }
};
