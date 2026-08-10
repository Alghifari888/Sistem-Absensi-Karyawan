<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('employment_type', ['tetap', 'kontrak'])->nullable()->after('role');
            $table->date('contract_start_date')->nullable()->after('employment_type');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employment_type', 'contract_start_date', 'contract_end_date']);
        });
    }
};