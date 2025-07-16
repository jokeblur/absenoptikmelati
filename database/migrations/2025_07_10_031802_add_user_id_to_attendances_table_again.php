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
            // Tambahkan kolom 'user_id' sebagai unsignedBigInteger
            // Ini penting karena user_id adalah foreign key ke tabel users.id
            // Pastikan kolom ini TIDAK ADA di tabel Anda sebelum menjalankan migrasi ini,
            // atau kolom yang merujuk ke tabel users sudah ada dengan nama lain.
            if (!Schema::hasColumn('attendances', 'user_id')) { // Cek dulu apakah kolom sudah ada
                $table->unsignedBigInteger('user_id')->nullable()->after('id'); // Sesuaikan posisi jika perlu
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Hapus kolom 'user_id' jika memang ada
            if (Schema::hasColumn('attendances', 'user_id')) {
                // Sebelum drop kolom, pastikan foreign key terkait sudah dihapus jika ada
                // Jika Anda mencoba drop foreign key secara terpisah, Anda mungkin perlu menangani itu di sini
                // Cek apakah FK attendances_user_id_foreign sudah ada sebelum drop kolom
                // Laravel 8+ bisa menggunakan $table->dropConstrainedForeignId('user_id');
                $table->dropColumn('user_id');
            }
        });
    }
};