<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom setelah kolom 'role'
            $table->decimal('gaji_pokok', 15, 2)->default(0)->after('role');
            $table->decimal('tarif_lembur_per_jam', 15, 2)->default(0)->after('gaji_pokok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gaji_pokok', 'tarif_lembur_per_jam']);
        });
    }
};
