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
     Schema::create('branches', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('address');
    $table->decimal('latitude', 10, 8); // presisi 10 digit, 8 desimal
    $table->decimal('longitude', 11, 8);
    $table->integer('attendance_radius')->default(100); // dalam meter
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
