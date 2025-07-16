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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Karyawan yang mengajukan izin
            $table->date('permission_date'); // Tanggal izin
            $table->text('reason'); // Alasan izin
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->string('admin_notes')->nullable(); // Catatan dari admin (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};