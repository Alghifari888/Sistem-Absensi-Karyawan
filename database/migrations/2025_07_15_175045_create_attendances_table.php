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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('attendance_date');
            $table->time('check_in_time')->nullable();
            $table->text('check_in_location')->nullable(); // Format: "latitude,longitude"
            $table->time('check_out_time')->nullable();
            $table->text('check_out_location')->nullable(); // Format: "latitude,longitude"
            $table->timestamps();

            // Menambahkan unique constraint agar satu user hanya bisa absen sekali per hari
            $table->unique(['user_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};