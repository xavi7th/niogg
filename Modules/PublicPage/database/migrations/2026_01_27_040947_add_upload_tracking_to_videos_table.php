<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('videos', function (Blueprint $table): void {
      $table->string('upload_id')->nullable()->after('id')->index();
      $table->unsignedBigInteger('file_size')->nullable()->after('video_url');
      $table->string('mime_type')->nullable()->after('file_size');
      $table->string('original_filename')->nullable()->after('mime_type');
    });
  }

  public function down(): void
  {
    Schema::table('videos', function (Blueprint $table): void {
      $table->dropIndex(['upload_id']);
      $table->dropColumn(['upload_id', 'file_size', 'mime_type', 'original_filename']);
    });
  }
};
