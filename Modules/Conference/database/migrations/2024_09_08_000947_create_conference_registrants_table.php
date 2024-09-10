<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('conference_registrants', function (Blueprint $table): void {
      $table->id();
      $table->string('full_name');
      $table->string('email');
      $table->string('phone');
      $table->string('password');
      $table->string('registration_id');

      $table->string('conference_tag')->comment('The tag of the conference that they are registering for, This application can accomodate multiple conferences.');

      $table->timestamps();
      $table->timestamp('registration_processed_at')->nullable();
      $table->timestamp('email_verified_at')->nullable();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('conference_registrants');
  }
};
