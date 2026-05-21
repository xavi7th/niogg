<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table): void {
            $table->enum('conversion_status', ['pending', 'converting', 'completed', 'failed'])
                ->nullable()
                ->default('pending')
                ->after('mime_type')
                ->index();

            $table->timestamp('conversion_started_at')
                ->nullable()
                ->after('conversion_status');

            $table->timestamp('conversion_completed_at')
                ->nullable()
                ->after('conversion_started_at');

            $table->text('conversion_error')
                ->nullable()
                ->after('conversion_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table): void {
            $table->dropColumn([
                'conversion_status',
                'conversion_started_at',
                'conversion_completed_at',
                'conversion_error',
            ]);
        });
    }
};
