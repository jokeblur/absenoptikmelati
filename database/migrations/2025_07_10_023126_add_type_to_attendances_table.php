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
            // Tambahkan kolom 'type' dengan tipe data string (misalnya 'masuk', 'pulang')
            // Beri panjang string yang cukup, misalnya 20 karakter
            $table->string('type', 20)->nullable()->after('check_in'); // Sesuaikan posisi jika perlu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};