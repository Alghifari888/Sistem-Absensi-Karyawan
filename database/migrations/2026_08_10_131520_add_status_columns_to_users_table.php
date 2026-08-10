<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
            $table->text('deactivated_reason')->nullable()->after('is_active');
            $table->timestamp('deactivated_at')->nullable()->after('deactivated_reason');
            $table->foreignId('deactivated_by')->nullable()->after('deactivated_at')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['deactivated_by']);
            $table->dropColumn(['is_active', 'deactivated_reason', 'deactivated_at', 'deactivated_by']);
        });
    }
};