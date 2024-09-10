<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('payment_transactions', function (Blueprint $table): void {
      $table->id();
      $table->string('purchased_item_id')->nullable();
      $table->string('purchased_item_type')->nullable();
      $table->decimal('amount', 15, 2);
      $table->string('description');
      $table->string('transaction_reference');
      $table->string('payment_provider')->default('PAYSTACK');
      $table->text('api_response')->nullable();

      $table->timestamps();
      $table->timestamp('processed_at')->nullable();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('payment_transactions');
  }
};
