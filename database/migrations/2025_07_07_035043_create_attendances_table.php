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
    $table->foreignId('employee_id')->constrained();
    $table->date('date');
    $table->time('check_in')->nullable();
    $table->time('check_out')->nullable();
    $table->decimal('latitude_in', 10, 8)->nullable();
    $table->decimal('longitude_in', 11, 8)->nullable();
    $table->decimal('latitude_out', 10, 8)->nullable();
    $table->decimal('longitude_out', 11, 8)->nullable();
    $table->string('status_in')->default('on_time');
    $table->string('status_out')->nullable();
    $table->string('photo_in')->nullable(); // Foto selfie
    $table->string('photo_out')->nullable();
    $table->timestamps();
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
