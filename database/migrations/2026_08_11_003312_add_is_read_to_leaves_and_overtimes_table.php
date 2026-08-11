<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->boolean('is_read')->default(true)->after('approver_notes');
        });

        Schema::table('overtimes', function (Blueprint $table) {
            $table->boolean('is_read')->default(true)->after('approver_notes');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });

        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};