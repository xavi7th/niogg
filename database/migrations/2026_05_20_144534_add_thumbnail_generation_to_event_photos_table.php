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
    Schema::table('event_photos', function (Blueprint $table): void {
      $table->string('thumbnail_url')->nullable()->change();
      $table->string('thumbnail_generation')->nullable()->after('thumbnail_url');
    });
  }

  public function down(): void
  {
    Schema::table('event_photos', function (Blueprint $table): void {
      $table->dropColumn('thumbnail_generation');
      $table->string('thumbnail_url')->nullable(FALSE)->change();
    });
  }
};
