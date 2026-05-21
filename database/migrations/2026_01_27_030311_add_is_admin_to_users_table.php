<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_admin')->default(FALSE)->after('remember_token');
            $table->boolean('is_super_admin')->default(FALSE)->after('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['is_admin', 'is_super_admin']);
        });
    }
};
