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
        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan kolom 'timestamp' dengan tipe data datetime
            // Menggunakan 'after()' untuk menempatkan kolom setelah 'user_id'
            // Pastikan 'user_id' sudah ada. Jika tidak, sesuaikan posisinya.
            $table->dateTime('timestamp')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('timestamp');
        });
    }
};